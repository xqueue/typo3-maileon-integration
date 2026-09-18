<?php

namespace XQueue\Typo3MaileonIntegration\Services\Maileon;

use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MaileonApiClient
{
    public const SYNC_MODE_UPDATE = 1;

    private const XML_MIME_TYPE = 'application/vnd.maileon.api+xml';

    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUri = 'https://api.maileon.com/1.0',
    ) {
    }

    public function getContactByEmail(string $email): MaileonApiResult
    {
        return $this->request('GET', 'contacts/email/' . rawurlencode($email));
    }

    public function createContact(
        Contact $contact,
        int $syncMode,
        string $source,
        string $subscriptionForm,
        ?bool $doi,
        ?bool $doiPlus,
        ?string $doiMailingKey
    ): MaileonApiResult {
        $query = [
            'sync_mode' => $syncMode,
            'src' => $source,
            'subscription_page' => $subscriptionForm,
        ];

        if ($doi !== null) {
            $query['doi'] = $doi ? 'true' : 'false';
        }
        if ($doiPlus !== null) {
            $query['doiplus'] = $doiPlus ? 'true' : 'false';
        }
        if (trim((string)$doiMailingKey) !== '') {
            $query['doimailing'] = trim($doiMailingKey);
        }
        if ($contact->permission !== null) {
            $query['permission'] = Permission::getCode($contact->permission);
        }

        return $this->request(
            'POST',
            'contacts/email/' . rawurlencode($contact->email),
            $query,
            $contact->toXml()
        );
    }

    public function unsubscribeContactByEmail(string $email): MaileonApiResult
    {
        return $this->request('DELETE', 'contacts/email/' . rawurlencode($email) . '/unsubscribe');
    }

    public function getCustomFields(): MaileonApiResult
    {
        return $this->request('GET', 'contacts/fields/custom');
    }

    public function createCustomField(string $fieldName, string $fieldType): MaileonApiResult
    {
        return $this->request(
            'POST',
            'contacts/fields/custom/' . rawurlencode($fieldName),
            ['type' => $fieldType],
            ''
        );
    }

    public function pingGet(): MaileonApiResult
    {
        return $this->request('GET', 'ping');
    }

    public function pingPost(): MaileonApiResult
    {
        return $this->request('POST', 'ping', [], 'foobar');
    }

    public function pingPut(): MaileonApiResult
    {
        return $this->request('PUT', 'ping', [], '');
    }

    public function getAccountInfo(): MaileonApiResult
    {
        return $this->request('GET', 'account/info', [], null, 'application/json');
    }

    private function request(
        string $method,
        string $path,
        array $query = [],
        ?string $body = null,
        string $mimeType = self::XML_MIME_TYPE
    ): MaileonApiResult {
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        $options = [
            'headers' => [
                'Accept' => $mimeType,
                'Content-type' => $mimeType,
                'Authorization' => 'Basic ' . base64_encode($this->apiKey),
            ],
            'http_errors' => false,
        ];

        if (!empty($query)) {
            $options['query'] = $query;
        }
        if ($body !== null) {
            $options['body'] = $body;
        }

        $response = $requestFactory->request($this->baseUri . '/' . $path, $method, $options);

        return new MaileonApiResult($response);
    }
}
