<?php

namespace XQueue\Typo3MaileonIntegration\Services\Maileon;

class Contact
{
    public string $email = '';
    public array $standard_fields = [];
    public array $custom_fields = [];
    public ?string $permission = null;

    public function toXml(): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0"?><contact/>');
        $xml->addChild('email', $this->email);

        $standardFields = $xml->addChild('standard_fields');
        foreach ($this->standard_fields as $name => $value) {
            $this->addField($standardFields, (string)$name, $value);
        }

        $customFields = $xml->addChild('custom_fields');
        foreach ($this->custom_fields as $name => $value) {
            $this->addField($customFields, (string)$name, $value);
        }

        $xml->addChild('preferences');

        return $xml->asXML();
    }

    private function addField(\SimpleXMLElement $parent, string $name, mixed $value): void
    {
        $field = $parent->addChild('field');
        $field->addChild('name', $name);

        $valueNode = dom_import_simplexml($field->addChild('value'));
        $valueNode->appendChild($valueNode->ownerDocument->createCDATASection($this->stringifyFieldValue($value)));
    }

    private function stringifyFieldValue(mixed $value): string
    {
        return is_bool($value) ? (string)(int)$value : (string)$value;
    }
}
