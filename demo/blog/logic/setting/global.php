<?php
return [
    'rdb' => [
        'sources' => [
            'main' => [
                'master' => 'sqlite://' . swap\var_dir . '/repo/rdb.db',
                'slaves' => [
                    'sqlite://' . swap\var_dir . '/repo/rdb.db',
                    'sqlite://' . swap\var_dir . '/repo/rdb.db',
                ],
            ],
        ],
        'tables' => [
            '*' => 'main',
        ],
    ],
    'cache' => [
        'setting' => [
            'filesys://' . swap\var_dir . '/cache/data/setting',
        ],
    ],
];
