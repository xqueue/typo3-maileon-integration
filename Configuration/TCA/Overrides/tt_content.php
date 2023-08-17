<?php
defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionUtility::registerPlugin(
    'Typo3MaileonIntegration',
    'Subscribe',
    'Subscription form',
    'EXT:typo3_maileon_integration/Resources/Public/Icons/Extension.png'
);


ExtensionUtility::registerPlugin(
    'Typo3MaileonIntegration',
    'Unsubscribe',
    'Unsubscription form',
    'EXT:typo3_maileon_integration/Resources/Public/Icons/Extension.png'
);


$pluginSignature = 'typo3maileonintegration_subscribe';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    // Flexform configuration schema file
    'FILE:EXT:typo3_maileon_integration/Configuration/FlexForms/Subscribe.xml'
);


$pluginSignature = 'typo3maileonintegration_unsubscribe';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    // Flexform configuration schema file
    'FILE:EXT:typo3_maileon_integration/Configuration/FlexForms/Unsubscribe.xml'
);
