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
];
