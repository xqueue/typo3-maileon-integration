<?php

namespace XQueue\Typo3MaileonIntegration\Services;

use DateTime;
use DateTimeInterface;
use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use de\xqueue\maileon\api\client\utils\PingService;
use Exception;
use InvalidArgumentException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Form\Domain\Model\FormDefinition;
use XQueue\Typo3MaileonIntegration\Domain\Repository\XQHbSendRepository;
use XQueue\Typo3MaileonIntegration\Settings\Settings;

class FormProcessingService
{
    protected array $maileonConfig = [];
    protected XQHbSendRepository $xqHbSendRepository;
    protected HeartBeatService $heartBeatService;

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws Exception
     */
    public function __construct()
    {
        $extensionConfig = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get(Settings::EXTENSION_KEY);

        $apiKey = $extensionConfig['apiKey'] ?? null;

        $this->maileonConfig = [
            'BASE_URI' => 'https://api.maileon.com/1.0',
            'API_KEY' => $apiKey,
            'TIMEOUT' => 30,
        ];

        if (empty($apiKey) || !$this->isMaileonApiKeyValid()) {
            throw new Exception('Missing or invalid Maileon API key in extension configuration.');
        }

        $this->xqHbSendRepository = GeneralUtility::makeInstance(XQHbSendRepository::class);
        $this->heartBeatService = new HeartBeatService($apiKey);
    }

    /**
     * @throws Exception
     */
    public function processSubscribeForm(array $formData, FormDefinition $formDefinition, array $finisherSettings): void
    {
        [$email, $standardFields, $customFields] = $this->extractFormValues($formData, $formDefinition);

        if ($email === null || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('No valid email found in form field with Maileon field name "email".');
        }

        $this->checkAndCreateCustomFields($customFields);

        foreach ($customFields as $key => $value) {
            $parts = explode('|', $value, 2);
            $customFields[$key] = $parts[0] ?? null;
        }

        $contact = $this->buildContact($email, $standardFields, $customFields);
        $contact->permission = Permission::getPermission($finisherSettings['permission'] ?? 'none');

        $this->trySubscribeContact($contact, $finisherSettings);
    }

    /**
     * @throws Exception
     */
    public function processUnsubscribeForm(array $formData, FormDefinition $formDefinition): void
    {
        [$email] = $this->extractFormValues($formData, $formDefinition);

        if ($email === null || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('No valid email found in form field with Maileon field name "email".');
        }

        $this->tryUnsubscribeContact($email);
    }

    protected function extractFormValues(array $formData, FormDefinition $formDefinition): array
    {
        $standardFields = [];
        $customFields = [
            "Typo3_created" => true,
        ];
        $email = null;

        foreach ($formDefinition->getElements() ?? [] as $element) {
            $identifier = $element->getIdentifier() ?? '';
            $properties = $element->getProperties();
            $maileonFieldName = $properties['maileonFieldName'] ?? null;
            $type = $element->getType() ?? null;

            if (!$maileonFieldName || !isset($formData[$identifier])) {
                continue;
            }

            $value = $this->convertValueByType($type, $formData[$identifier]);
            $lowerCaseName = strtolower($maileonFieldName);

            if ($lowerCaseName === 'email') {
                $email = $value;
                continue;
            }

            if (in_array($lowerCaseName, Settings::STANDARD_FIELDS, true)) {
                $standardFields[strtoupper($lowerCaseName)] = $value;
            } else {
                $customFields[$maileonFieldName] = $value . '|' . $this->resolveMaileonCustomFieldType($type);
            }
        }

        return [$email, $standardFields, $customFields];
    }

    protected function validateStandardFields(array $standardFields): void
    {
        if (isset($standardFields['GENDER']) && !in_array(strtolower($standardFields['GENDER']), ['f', 'm', 'd'], true)) {
            throw new InvalidArgumentException('Invalid value for gender. Allowed: f, m, d.');
        }

        if (isset($standardFields['LOCALE']) && !preg_match('/^[a-z]{2}$/i', $standardFields['LOCALE'])) {
            throw new InvalidArgumentException(
                'Invalid locale format. Expected a two-letter language code like "en", "de", or "hu".'
            );
        }
    }

    protected function convertValueByType(string $type, mixed $value): mixed
    {
        switch ($type) {
            case 'Checkbox':
                return (bool)$value;

            case 'Date':
                if ($value instanceof DateTimeInterface) {
                    return $value->format('Y-m-d');
                }
                $date = DateTime::createFromFormat('Y-m-d', $value);
                return $date ? $date->format('Y-m-d') : $value;

            case 'Number':
                return (int)$value;

            default:
                return mb_substr((string)$value, 0, 255);
        }
    }

    /**
     * @throws UnknownObjectException
     * @throws IllegalObjectTypeException
     * @throws InvalidQueryException
     */
    public function trySubscribeContact(Contact $contact, array $finisherSettings): MaileonAPIResult
    {
        $contactsService = $this->getContactsService();
        $getContactByEmail = $contactsService->getContactByEmail($contact->email);

        $withDoi = !$getContactByEmail->isSuccess() || Permission::$NONE === $getContactByEmail->getResult()->permission;

        $response = $contactsService->createContact(
            $contact,
            SynchronizationMode::$UPDATE,
            'Typo3',
            'subscriptionForm',
            $withDoi ? $finisherSettings['enableDoiProcess'] : null,
            $withDoi ? $finisherSettings['enableDoiProcess'] : null,
            $withDoi ? $finisherSettings['doiKey'] : null
        );

        $this->handleHB();

        return $response;
    }

    /**
     * @throws UnknownObjectException
     * @throws IllegalObjectTypeException
     * @throws InvalidQueryException
     */
    public function tryUnsubscribeContact(string $email): MaileonAPIResult
    {
        $contactsService = $this->getContactsService();
        $this->handleHB();

        return $contactsService->unsubscribeContactByEmail($email);
    }

    /**
     * Returns contacts service from Maileon API
     */
    protected function getContactsService(): ContactsService
    {
        return new ContactsService($this->maileonConfig);
    }

    /**
     * Returns ping service from Maileon API
     */
    protected function getPingService(): PingService
    {
        return new PingService($this->maileonConfig);
    }

    protected function isMaileonApiKeyValid(): bool
    {
        $pingService = $this->getPingService();

        return $pingService->pingGet()->isSuccess()
            && $pingService->pingPost()->isSuccess()
            && $pingService->pingPut()->isSuccess();
    }

    /**
     * Create Contact obj
     */
    protected function buildContact(string $email, array $standardFields, array $customFields): Contact
    {
        $contact = new Contact();
        $contact->email = $email;

        $this->validateStandardFields($standardFields);
        $contact->standard_fields = $standardFields;

        $contact->custom_fields = $customFields;

        return $contact;
    }

    /**
     * Check the custom fields exist at Maileon. If not create it.
     */
    protected function checkAndCreateCustomFields(array $customFields): void
    {
        $contactsService = $this->getContactsService();
        $existingFields = $contactsService->getCustomFields()->getResult()->custom_fields;

        if (!array_key_exists('Typo3_created', $existingFields)) {
            $contactsService->createCustomField('Typo3_created', 'boolean');
        }

        foreach ($customFields as $fieldName => $fieldData) {
            if (!array_key_exists($fieldName, $existingFields)) {
                $parts = explode('|', $fieldData);
                $contactsService->createCustomField($fieldName, $parts[1] ?? 'string');
            }
        }
    }

    /**
     * @throws UnknownObjectException
     * @throws IllegalObjectTypeException
     */
    protected function handleHB(): void
    {
        $task = $this->xqHbSendRepository->findByTask('maileon_hb');

        if (empty($task) || !$this->xqHbSendRepository->hasTaskRunToday('maileon_hb')) {
            $this->heartBeatService->sendHeartbeat();
            $this->xqHbSendRepository->updateLastExecution('maileon_hb');
        }
    }

    protected function resolveMaileonCustomFieldType(string $formFieldType): string
    {
        return Settings::MAILEON_FIELD_TYPE_MAP[$formFieldType] ?? 'string';
    }
}
