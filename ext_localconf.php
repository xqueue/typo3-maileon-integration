<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die('Access denied.');

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['maileon_api_validation'] ?? null)) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['maileon_api_validation'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
        'options' => [
            'defaultLifetime' => 600,
        ],
        'groups' => ['system'],
    ];
}

ExtensionManagementUtility::addTypoScriptSetup(
'module.tx_form {
        settings {
            yamlConfigurations {
                1749823796 = EXT:typo3_maileon_integration/Configuration/Yaml/MaileonFormSetup.yaml
            }
        }
    }'
);
