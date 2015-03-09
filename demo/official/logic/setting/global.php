<?php
return [
    'rdb' => [
        'sources' => [
            'main' => [
                'master' => 'sqlite://' . swap\run_dir . '/repo/rdb.db',
                'slaves' => [
                    'sqlite://' . swap\run_dir . '/repo/rdb.db',
                    'sqlite://' . swap\run_dir . '/repo/rdb.db',
                ],
            ],
        ],
        'tables' => [
            '*' => 'main',
        ],
    ],
];
