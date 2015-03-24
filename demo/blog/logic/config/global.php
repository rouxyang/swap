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
        'setting' => [
            'filesys://' . kern\data_dir . '/cache/data/setting',
        ],
    ],
];
