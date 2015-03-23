<?php
// 全局默认配置文件
return [
    'kern' => [
        'secret_key'        => 'please change me!',
        'log_execute_time'  => true,
        'send_x_powered_by' => true,
        'is_debug'          => true,
        'log_errors'        => true,
        'log_with_trace'    => true,
        'log_rotate_method' => 'day',
        'error_reporting'   => E_ALL | E_STRICT,
        'display_errors'    => true,
        'locale'            => 'zh_cn',
        'time_zone'         => 'Asia/Shanghai',
        'module_domain'     => '127.0.0.1:8080',
        'static_domain'     => '',
        'upload_domain'     => '',
        'version_key'       => 'v',
        'version'           => 1,
    ],
    'modules' => [
        'base' => [
            'url' => [
                'domains'        => [],
                'is_https'       => false,
                'enable_rewrite' => false,
                'target_key'     => 't',
                'csrf_key'       => 's',
                'routes'         => [],
            ],
            'view' => [
                'default_title'       => '',
                'default_keywords'    => '',
                'default_description' => '',
                'default_author'      => '',
                'default_viewport'    => '',
                'default_skeleton'    => 'main', // 'name', false
                'minify_pps'          => false,
                'cache_pps_in_client' => false,
                'cache_pps_in_server' => false,
            ],
            'global_filters' => [
                'before' => [],
                'after'  => [],
            ],
            'visitor' => [
                'cookie_domain' => '',
                'roles' => [
                    'user' => [
                        'sid_name'              => 'user_sid',
                        'default_alive_seconds' => 3600,
                        'trace_last_active'     => true,
                        // 'session_dsn'        => 'memcached://ip:port',
                        // 'session_dsn'        => ['memcached://ip1:port1', 'memcached://ip2:port2'],
                        // 'session_dsn'        => 'mysql://user:pass@ip:port/db_name/table_name',
                        // 'session_dsn'        => 'pgsql://user:pass@ip:port/db_name/table_name',
                        'session_dsn'           => 'sqlite://' . kern\run_dir . '/session/session.db/user_session',
                    ],
                ],
            ],
        ],
        'admin' => [
            'url' => [
                'domains' => [],
            ],
        ],
        'mobile' => [
            'url' => [
                'domains' => [],
            ],
        ],
        'api' => [
            'url' => [
                'domains' => [],
            ],
        ],
    ],
];
