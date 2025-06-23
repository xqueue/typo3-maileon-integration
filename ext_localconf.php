<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;

defined('TYPO3') or die('Access denied.');

ExtensionManagementUtility::addTypoScriptSetup(
'module.tx_form {
        settings {
            yamlConfigurations {
                1749823796 = EXT:typo3_maileon_integration/Configuration/Yaml/MaileonFormSetup.yaml
            }
        }
    }'
);

$iconRegistry = GeneralUtility::makeInstance(
    IconRegistry::class
);

$iconRegistry->registerIcon(
    'Typo3MaileonIntegration',
    BitmapIconProvider::class,
    ['source' => 'EXT:typo3_maileon_integration/Resources/Public/Icons/Extension.png']
);
