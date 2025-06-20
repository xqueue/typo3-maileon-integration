<?php

return [
    'ctrl' => [
        'title' => 'Task Execution Log',
        'label' => 'task',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'searchFields' => 'task',
        'enablecolumns' => [],
        'default_sortby' => 'ORDER BY crdate DESC',
        'iconfile' => 'EXT:subscription_form/Resources/Public/Icons/hb_send.svg',
    ],
    'columns' => [
        'pid' => [  // Ensure pid is properly registered
            'exclude' => true,
            'label' => 'Page ID',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'task' => [
            'label' => 'Task',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim,required'
            ],
        ],
        'last_execution' => [
            'label' => 'Last Execution Timestamp',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'int',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'pid, task, last_execution'],
    ],
];