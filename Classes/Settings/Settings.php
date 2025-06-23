<?php

namespace XQueue\Typo3MaileonIntegration\Settings;

final class Settings
{
    public const EXTENSION_KEY = 'typo3_maileon_integration';
    public const XSIC_ID = '10023';
    public const XSIC_CHECKSUM = 'dbWIvEDI69UXf22mpRxqctkH';
    public const XSIC_URL = 'https://integrations.maileon.com/xsic/tx.php';
    public const STANDARD_FIELDS = [
        'fullname', 'lastname', 'firstname', 'birthday', 'address', 'city',
        'country', 'gender', 'hnr', 'locale', 'nameday', 'organization',
        'region', 'state', 'salutation', 'title', 'zip'
    ];
    public const MAILEON_FIELD_TYPE_MAP = [
        'Text' => 'string',
        'Textarea' => 'string',
        'Email' => 'string',
        'Telephone' => 'string',
        'Url' => 'string',
        'Number' => 'integer',
        'Date' => 'date',
        'Checkbox' => 'boolean',
        'SingleSelect' => 'string',
        'Hidden' => 'string',
    ];
}