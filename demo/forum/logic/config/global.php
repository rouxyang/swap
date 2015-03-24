<?php
return [
    'rdb' => [
        'sources' => [
            'main' => [
                'master' => 'sqlite://' . kern\data_dir . '/repo/rdb.db',
                'slaves' => [
                    'sqlite://' . kern\data_dir . '/repo/rdb.db',
                    'sqlite://' . kern\data_dir . '/repo/rdb.db',
                ],
            ],
        ],
        'tables' => [
            '*' => 'main',
        ],
    ],
    'cache' => [
        'misc' => [
            'filesys://' . kern\data_dir . '/cache/data/misc',
        ],
    ],
    'mover' => [
        'avatar' => 'filesys://' . kern\web_dir . '/upload/avatar',
    ],
];
