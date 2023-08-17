<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$extensionKey = 'typo3_maileon_integration';

/**
* Default TypoScript
*/
ExtensionManagementUtility::addStaticFile(
    $extensionKey,
    'Configuration/TypoScript/',
    'Configuration for Typo 3 - Maileon integration'
);
