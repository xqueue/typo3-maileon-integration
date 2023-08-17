<?php
defined('TYPO3_MODE') || die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(function () {
    $extensionKey = 'typo3_maileon_integration';

    /**
    * Default TypoScript
    */
    ExtensionManagementUtility::addStaticFile(
        $extensionKey,
        'Configuration/TypoScript',
        'Configuration for Typo 3 - Maileon integration'
    );
});
