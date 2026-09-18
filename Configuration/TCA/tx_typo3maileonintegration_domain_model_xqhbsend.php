<?php

return [
    'ctrl' => [
        'title' => 'XQ Heartbeat Send',
        'label' => 'task',
        'enablecolumns' => [],
        'hideTable' => true,
    ],
    'columns' => [
        'task' => [
            'label' => 'Task',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'required' => true,
                'eval' => 'trim',
            ],
        ],
        'last_execution' => [
            'label' => 'Last Execution',
            'config' => [
                'type' => 'number',
                'format' => 'integer',
            ],
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'task, last_execution'],
    ],
];