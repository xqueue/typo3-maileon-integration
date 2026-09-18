<?php

namespace XQueue\Typo3MaileonIntegration\Services\Maileon;

use Psr\Http\Message\ResponseInterface;

class MaileonApiResult
{
    private readonly string $body;

    public function __construct(private readonly ResponseInterface $response)
    {
        $this->body = (string)$response->getBody();
    }

    public function isSuccess(): bool
    {
        $status = $this->response->getStatusCode();

        return $status >= 200 && $status <= 299;
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getBodyData(): ?string
    {
        return $this->body !== '' ? $this->body : null;
    }

    public function getResult(): mixed
    {
        if ($this->body === '') {
            return null;
        }

        $contentType = $this->response->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'json')) {
            return json_decode($this->body);
        }

        if (str_contains($contentType, 'xml')) {
            return $this->parseXml();
        }

        return $this->body;
    }

    private function parseXml(): mixed
    {
        $previousSetting = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->body);
        libxml_use_internal_errors($previousSetting);

        if ($xml === false) {
            return null;
        }

        return match ($xml->getName()) {
            'contact' => $this->parseContactXml($xml),
            'custom_fields' => $this->parseCustomFieldsXml($xml),
            default => $xml,
        };
    }

    private function parseContactXml(\SimpleXMLElement $xml): \stdClass
    {
        $contact = new \stdClass();
        $contact->permission = Permission::getPermission((string)$xml->permission);

        return $contact;
    }

    private function parseCustomFieldsXml(\SimpleXMLElement $xml): \stdClass
    {
        $result = new \stdClass();
        $fields = [];

        foreach ($xml->custom_field as $field) {
            $fields[trim((string)$field->name)] = (string)$field->type;
        }

        $result->custom_fields = $fields;

        return $result;
    }
}
