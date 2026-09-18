<?php

namespace XQueue\Typo3MaileonIntegration\Services;

use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Form\Domain\Model\FormDefinition;
use XQueue\Typo3MaileonIntegration\Domain\Repository\XQHbSendRepository;
use XQueue\Typo3MaileonIntegration\Exception\MaileonIntegrationException;
use XQueue\Typo3MaileonIntegration\Services\Maileon\Contact;
use XQueue\Typo3MaileonIntegration\Services\Maileon\MaileonApiClient;
use XQueue\Typo3MaileonIntegration\Services\Maileon\MaileonApiResult;
use XQueue\Typo3MaileonIntegration\Services\Maileon\Permission;
use XQueue\Typo3MaileonIntegration\Settings\Settings;

class FormProcessingService
{
    use LoggerAwareTrait;

    protected array $maileonConfig = [];
    protected XQHbSendRepository $xqHbSendRepository;
    protected HeartBeatService $heartBeatService;

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws MaileonIntegrationException
     */
    public function __construct()
    {
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $this->setLogger($logManager->getLogger(__CLASS__));

        $extensionConfig = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get(Settings::EXTENSION_KEY);

        $apiKey = $extensionConfig['apiKey'] ?? null;

        $this->maileonConfig = [
            'BASE_URI' => 'https://api.maileon.com/1.0',
            'API_KEY' => $apiKey,
            'TIMEOUT' => 30,
        ];

        if (empty($apiKey) || !$this->isMaileonApiKeyValid($apiKey)) {
            $this->logger->error('Missing or invalid Maileon API key in extension configuration.');
            throw new MaileonIntegrationException('Missing or invalid Maileon API key in extension configuration.');
        }

        $this->xqHbSendRepository = GeneralUtility::makeInstance(XQHbSendRepository::class);
        $this->heartBeatService = new HeartBeatService($apiKey);
    }

    /**
     * @throws MaileonIntegrationException
     */
    public function processSubscribeForm(array $formData, FormDefinition $formDefinition, array $finisherSettings): void
    {
        [$email, $standardFields, $customFields] = $this->extractFormValues($formData, $formDefinition);

        if ($email === null || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new MaileonIntegrationException('No valid email found in form field with Maileon field name "email".');
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
     * @throws MaileonIntegrationException
     */
    public function processUnsubscribeForm(array $formData, FormDefinition $formDefinition): void
    {
        [$email] = $this->extractFormValues($formData, $formDefinition);

        if ($email === null || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new MaileonIntegrationException('No valid email found in form field with Maileon field name "email".');
        }

        $this->tryUnsubscribeContact($email);
    }

    protected function extractFormValues(array $formData, FormDefinition $formDefinition): array
    {
        $standardFields = [];
        $customFields = [
            'Typo3_created' => '1|boolean',
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
            throw new \InvalidArgumentException('Invalid value for gender. Allowed: f, m, d.');
        }

        if (isset($standardFields['LOCALE']) && !preg_match('/^[a-z]{2}$/i', $standardFields['LOCALE'])) {
            throw new \InvalidArgumentException(
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
                if ($value instanceof \DateTimeInterface) {
                    return $value->format('Y-m-d');
                }
                $date = \DateTime::createFromFormat('Y-m-d', $value);
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
    public function trySubscribeContact(Contact $contact, array $finisherSettings): MaileonApiResult
    {
        $contactsService = $this->getMaileonApiClient();
        $getContactByEmail = $contactsService->getContactByEmail($contact->email);

        $withDoi = !$getContactByEmail->isSuccess() || Permission::NONE === $getContactByEmail->getResult()->permission;
        $needDoiPlus = false;

        if ($finisherSettings['finalPermission'] === 'doi+') {
            $needDoiPlus = true;
        }

        $response = $contactsService->createContact(
            $contact,
            MaileonApiClient::SYNC_MODE_UPDATE,
            'Typo3',
            'subscriptionForm',
            $withDoi ? $finisherSettings['enableDoiProcess'] : null,
            $withDoi ? $needDoiPlus : null,
            $withDoi ? $finisherSettings['doiKey'] : null
        );

        if (!$response->isSuccess()) {
            $this->logger->error('Maileon createContact failed.', [
                'email' => $contact->email,
                'statusCode' => $response->getStatusCode(),
                'body' => $response->getBodyData(),
            ]);
            throw new MaileonIntegrationException('Failed to create/update Maileon contact for "' . $contact->email . '".');
        }

        $this->handleHB();

        return $response;
    }

    /**
     * @throws UnknownObjectException
     * @throws IllegalObjectTypeException
     * @throws InvalidQueryException
     */
    public function tryUnsubscribeContact(string $email): MaileonApiResult
    {
        $contactsService = $this->getMaileonApiClient();
        $response = $contactsService->unsubscribeContactByEmail($email);

        if (!$response->isSuccess()) {
            $this->logger->error('Maileon unsubscribeContactByEmail failed.', [
                'email' => $email,
                'statusCode' => $response->getStatusCode(),
                'body' => $response->getBodyData(),
            ]);
            throw new MaileonIntegrationException('Failed to unsubscribe Maileon contact "' . $email . '".');
        }

        $this->handleHB();

        return $response;
    }

    /**
     * Returns the Maileon API client
     */
    protected function getMaileonApiClient(): MaileonApiClient
    {
        return new MaileonApiClient($this->maileonConfig['API_KEY'], $this->maileonConfig['BASE_URI']);
    }

    protected function isMaileonApiKeyValid(string $apiKey): bool
    {
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('maileon_api_validation');
        $cacheKey = 'valid_' . sha1($apiKey);

        $cached = $cache->get($cacheKey);
        if ($cached !== false) {
            return (bool)$cached;
        }

        $pingService = $this->getMaileonApiClient();
        $isValid = $pingService->pingGet()->isSuccess()
            && $pingService->pingPost()->isSuccess()
            && $pingService->pingPut()->isSuccess();

        $cache->set($cacheKey, $isValid);

        return $isValid;
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
        $contactsService = $this->getMaileonApiClient();
        $existingFields = $contactsService->getCustomFields()->getResult()->custom_fields;

        foreach ($customFields as $fieldName => $fieldData) {
            if (!array_key_exists($fieldName, $existingFields)) {
                $parts = explode('|', $fieldData);
                $this->createCustomFieldWithLogging($contactsService, $fieldName, $parts[1] ?? 'string');
            }
        }
    }

    /**
     * A failed custom-field creation must not block the actual subscribe/unsubscribe
     * that follows (e.g. the field may already exist under a race), so this only logs.
     * A field that already exists is not an error (the preceding existence check is
     * a best-effort snapshot and can race with a concurrent/previous creation), so
     * that specific response is treated as a silent no-op rather than logged.
     */
    protected function createCustomFieldWithLogging(MaileonApiClient $contactsService, string $fieldName, string $fieldType): void
    {
        $response = $contactsService->createCustomField($fieldName, $fieldType);

        if ($response->isSuccess() || $this->isFieldAlreadyExistsResponse($response)) {
            return;
        }

        $this->logger->warning('Maileon createCustomField failed.', [
            'fieldName' => $fieldName,
            'fieldType' => $fieldType,
            'statusCode' => $response->getStatusCode(),
            'body' => $response->getBodyData(),
        ]);
    }

    protected function isFieldAlreadyExistsResponse(MaileonApiResult $response): bool
    {
        return $response->getStatusCode() === 400
            && str_contains((string)$response->getBodyData(), 'already exists');
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
