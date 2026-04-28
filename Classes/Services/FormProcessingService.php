<?php

namespace XQueue\Typo3MaileonIntegration\Services;

use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;
use de\xqueue\maileon\api\client\MaileonAPIResult;
use de\xqueue\maileon\api\client\utils\PingService;

class FormProcessingService
{
    protected array $settings;

    protected array $maileonConfig;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
        $this->maileonConfig = [
            'BASE_URI' => 'https://api.maileon.com/1.0',
            'API_KEY' => $this->settings['apiKey'],
            'TIMEOUT' => 30,
        ];
    }

    /**
     * Validate form data
     */
    public function validateData(array $data): array
    {
        $errors = [];

        // Check API key
        if (empty($this->settings['apiKey'])) {
            $errors['apiKey'] = 'Maileon API key is required';
            return $errors;
        }

        if (! $this->isMaileonApiKeyValid()) {
            $errors['apiKey'] = 'Maileon API is invalid';
            return $errors;
        }

        // Required fields validation
        $requiredFields = ['email'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }

        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }

        if (! $data['approval']) {
            $errors['approval'] = 'Privacy is required.';
        }

        if (! $data['privacy']) {
            $errors['privacy'] = 'Privacy is required.';
        }

        return $errors;
    }

    public function trySubscribeContact(array $data): MaileonAPIResult
    {
        $contactsService = $this->getContactsService();

        $forceDoi = ($this->settings['forceDoiOnExistingContact'] ?? '0') === '1';
        $targetPermission = (int)($this->settings['targetPermission'] ?? 0);
        $targetPermissionIsDoiPlus = $targetPermission === Permission::$DOI_PLUS->code;

        $doiMailingKey = $this->settings['doiMailingKey'] ?? '';

        $existingContactResponse = $contactsService->getContactByEmail($data['email']);
        $newContact = $this->createNewContact($data);

        // CASE 1: Contact does NOT exist
        if (!$existingContactResponse->isSuccess()) {
            return $this->createContactWithOptionalDoiPlus(
                $contactsService,
                $newContact,
                $targetPermissionIsDoiPlus,
                $doiMailingKey
            );
        }

        $existingContact = $existingContactResponse->getResult();

        // CASE 2: Exists, but NO permission
        if ($existingContact->permission === Permission::$NONE) {
            return $this->createContactWithOptionalDoiPlus(
                $contactsService,
                $newContact,
                $targetPermissionIsDoiPlus,
                $doiMailingKey
            );
        }

        // CASE 3: Exists with permission
        if ($forceDoi) {
            return $contactsService->updateContact(
                $existingContact,
                '',
                'Typo3',
                'subscriptionForm',
                true, // force DOI
                $doiMailingKey,
                true
            );
        }

        // CASE 4: Exists with permission, no DOI
        return $contactsService->createContact(
            $newContact,
            SynchronizationMode::$UPDATE,
            'Typo3',
            'subscriptionForm'
        );
    }

    public function tryUnsubscribeContact(string $email): MaileonAPIResult
    {
        $contactsService = $this->getContactsService();
        return $contactsService->unsubscribeContactByEmail($email);
    }

    /**
     * Returns contacts service from Maileon API
     */
    protected function getContactsService(): ContactsService
    {
        $contactsService = new ContactsService($this->maileonConfig);
        $contactsService->setDebug(false);

        return $contactsService;
    }

    /**
     * Returns ping service from Maileon API
     */
    protected function getPingService(): PingService
    {
        $pingService = new PingService($this->maileonConfig);
        $pingService->setDebug(false);

        return $pingService;
    }

    protected function isMaileonApiKeyValid(): bool
    {
        $pingService = $this->getPingService();

        $responseGet = $pingService->pingGet();
        $responsePost = $pingService->pingPost();
        $responsePut = $pingService->pingPut();

        if ($responseGet->getStatusCode() === 401) {
            return false;
        }

        if (! $responseGet->isSuccess()) {
            return false;
        }

        if (! $responsePost->isSuccess()) {
            return false;
        }

        if (! $responsePut->isSuccess()) {
            return false;
        }

        return true;
    }

    /**
     * Create Contact obj
     */
    protected function createNewContact(array $data): Contact
    {
        $newContact = new Contact();
        $newContact->anonymous = false;

        $newContact->email = $data['email'];

        $matchingCustomFields = $this->getMatchingCustomFields($data);

        // Standard and custom fields
        $standardFields = $this->extractMaileonStandardFields($data);
        $customFields = $this->extractMaileonCustomFields($matchingCustomFields, $data);

        $this->checkAndCreateCustomFields($matchingCustomFields);

        $newContact->standard_fields = $standardFields;
        $newContact->custom_fields = $customFields;

        return $newContact;
    }

    protected function createContactWithOptionalDoiPlus(
        ContactsService $contactsService,
        Contact $contact,
        bool $targetPermissionIsDoiPlus,
        string $doiMailingKey
    ): MaileonAPIResult {
        return $contactsService->createContact(
            $contact,
            SynchronizationMode::$UPDATE,
            'Typo3',
            'subscriptionForm',
            true,
            $targetPermissionIsDoiPlus,
            $doiMailingKey
        );
    }

    /**
     * Get array for standard fields
     */
    protected function extractMaileonStandardFields(array $data): array
    {
        return [
            "SALUTATION" => $this->getStringKeyValue($data, 'salutation'),
            "FIRSTNAME" => $this->getStringKeyValue($data, 'firstname'),
            "LASTNAME" => $this->getStringKeyValue($data, 'lastname'),
            "ORGANIZATION" => $this->getStringKeyValue($data, 'organization'),
        ];
    }

    /**
     * Get array for custom fields
     */
    protected function extractMaileonCustomFields(array $matchingCustomFields, array $data): array
    {
        if (empty($matchingCustomFields)) {
            return [];
        }

        $customFields = [
            "Typo3_created" => true,
        ];

        foreach ($matchingCustomFields as $customField) {
            if ($customField['dataType'] === 'boolean') {
                $customFields[$customField['name']] = $this->getBooleanKeyValue($data, $customField['name']);
                continue;
            }

            $customFields[$customField['name']] = $this->getStringKeyValue($data, $customField['name']);
        }

        return $customFields;
    }

    /**
     * Check the custom fields exist at Maileon. If not create it.
     */
    protected function checkAndCreateCustomFields(array $customFields): void
    {
        $contactsService = $this->getContactsService();

        $customFieldsResponse = $contactsService->getCustomFields();
        $customFieldsFromMaileon = $customFieldsResponse->getResult();

        if (!array_key_exists('Typo3_created', $customFieldsFromMaileon->custom_fields)) {
            $contactsService->createCustomField('Typo3_created', 'boolean');
        }

        foreach ($customFields as $field) {
            if (!array_key_exists($field['name'], $customFieldsFromMaileon->custom_fields)) {
                $contactsService->createCustomField($field['name'], $field['dataType']);
            }
        }
    }

    protected function getMatchingCustomFields(array $data): array
    {
        $matchingFields = [];
        $customFieldsFromSettings = $this->settings['subscribeForm']['customFields'] ?? [];

        foreach ($customFieldsFromSettings as $field) {
            if (!empty($field['name']) && isset($data[$field['name']])) {
                if ($field['dataType'] === 'string' || $field['dataType'] === 'boolean') {
                    $type = $field['dataType'];
                } else {
                    $type = 'string';
                }

                $matchingFields[] = [
                    'name' => $field['name'],
                    'dataType' => $type
                ];
            }
        }

        return $matchingFields;
    }

    protected function getStringKeyValue(array $array, string $key): string
    {
        return !empty($array[$key]) ? (string)$array[$key] : '';
    }

    protected function getBooleanKeyValue(array $array, string $key): bool
    {
        return !empty($array[$key]);
    }
}
