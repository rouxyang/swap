<?php
// 全局 logic 部分配置文件
return [
    'rdb' => [
        'sources' => [
            'main' => [
                // 'master' => 'mysql://user:pass@ip:port/db_name',
                // 'master' => 'pgsql://user:pass@ip:port/db_name',
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
    'redis' => [
        'demo1' => [
            'master' => 'redis://ip0:port0',
            'slaves' => [
                'redis://ip1:port1',
                'redis://ip2:port2',
            ],
        ],
        'demo2' => [
            'master' => 'redis://ip3:port3',
            'slaves' => [
                'redis://ip4:port4',
                'redis://ip5:port5',
            ],
        ],
    ],
    'cache' => [
        'demo1' => [
            'memcached://ip1:port1',
            'memcached://ip2:port2',
        ],
        'demo2' => [
            'redis://ip3:port3',
            'redis://ip4:port4',
        ],
    ],
    'mover' => [
        'demo' => 'filesys://' . kern\web_dir . '/upload/demo',
    ],
];
