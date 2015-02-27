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
        'misc' => [
            'filesys://' . swap\var_dir . '/cache/data/misc',
        ],
    ],
    'mover' => [
        'avatar' => 'filesys://' . swap\web_dir . '/upload/avatar',
    ],
];
