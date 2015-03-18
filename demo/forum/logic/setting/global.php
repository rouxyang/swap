<?php
return [
    'rdb' => [
        'sources' => [
            'main' => [
                'master' => 'sqlite://' . kern\run_dir . '/repo/rdb.db',
                'slaves' => [
                    'sqlite://' . kern\run_dir . '/repo/rdb.db',
                    'sqlite://' . kern\run_dir . '/repo/rdb.db',
                ],
            ],
        ],
        'tables' => [
            '*' => 'main',
        ],
    ],
    'cache' => [
        'misc' => [
            'filesys://' . kern\run_dir . '/cache/data/misc',
        ],
    ],
    'mover' => [
        'avatar' => 'filesys://' . kern\web_dir . '/upload/avatar',
    ],
];
