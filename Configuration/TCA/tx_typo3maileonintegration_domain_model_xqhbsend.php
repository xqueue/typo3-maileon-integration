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
                'eval' => 'trim,required',
            ],
        ],
        'last_execution' => [
            'label' => 'Last Execution',
            'config' => [
                'type' => 'input',
                'eval' => 'int',
            ],
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'task, last_execution'],
    ],
];