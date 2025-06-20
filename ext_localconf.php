<?php

declare(strict_types=1);

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use XQueue\Typo3MaileonIntegration\Controller\SubscribeController;

defined('TYPO3') or die('Access denied.');

ExtensionUtility::configurePlugin(
    'Typo3MaileonIntegration',
    'Subscribe',
    [SubscribeController::class => 'show, subscribe'],
    [SubscribeController::class => 'subscribe']
);

ExtensionUtility::configurePlugin(
    'Typo3MaileonIntegration',
    'Unsubscribe',
    [SubscribeController::class => 'unsubscribe'],
    [SubscribeController::class => 'unsubscribe']
);

$iconRegistry = GeneralUtility::makeInstance(
    IconRegistry::class
);

$iconRegistry->registerIcon(
    'Typo3MaileonIntegration',
    BitmapIconProvider::class,
    ['source' => 'EXT:typo3_maileon_integration/Resources/Public/Icons/Extension.png']
);
