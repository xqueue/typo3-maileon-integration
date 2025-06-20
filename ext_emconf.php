<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "typo3_maileon_integration".
 *
 * Auto generated 05-10-2015 13:46
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Maileon newsletter subscription',
    'description' => 'Add double-opt-in newsletter subscription form connected with Maileon to your Typo3 sites',
    'category' => 'plugin',
    'author' => 'Integrations Department',
    'author_email' => 'integrations@xqueue.com',
    'author_company' => 'XQueue GmbH',
    'state' => 'stable',
    'version' => '3.2.1',
    'clearCacheOnLoad' => true,
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'classmap' => [
           'Classes',
           'Resources/Private/Contrib/xqueue/maileon-api-client/src'
        ],
        'psr-4' => [
            'XQueue\\Typo3MaileonIntegration\\' => 'Classes',
            'de\\xqueue\\maileon\\api\\client\\' => 'Resources/Private/Contrib/xqueue/maileon-api-client/src'
         ]
     ],
];
