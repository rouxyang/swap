<?php
return [
    'rdb' => [
        'sources' => [
            'main' => [
                'master' => 'sqlite://' . swap\data_dir . '/repo/rdb.db',
                'slaves' => [
                    'sqlite://' . swap\data_dir . '/repo/rdb.db',
                    'sqlite://' . swap\data_dir . '/repo/rdb.db',
                ],
            ],
        ],
        'tables' => [
            '*' => 'main',
        ],
    ],
    'cache' => [
        'misc' => [
            'filesys://' . swap\data_dir . '/cache/data/misc',
        ],
    ],
    'mover' => [
        'avatar' => 'filesys://' . swap\web_dir . '/upload/avatar',
    ],
];
