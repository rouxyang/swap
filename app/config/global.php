<?php
// 全局默认配置文件
return [
    'swap' => [
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
        'static_domain'     => '',
        'upload_domain'     => '',
        'version_key'       => 'v',
        'version'           => 1,
    ],
    'base' => [
        'url' => [
            'domains'        => ['127.0.0.1:8080'],
            'is_https'       => false,
            'enable_rewrite' => false,
            'target_key'     => 't',
            'csrf_key'       => 's',
            'routes' => [
                '/about' => 'site/about',
            ],
        ],
        'view' => [
            'default_title'       => 'web 应用程序标题',
            'default_keywords'    => 'web, 应用, 程序, 关键字',
            'default_description' => 'web 应用程序描述。',
            'default_author'      => 'web 应用程序作者',
            'default_viewport'    => '',
            'use_skeleton'        => true,
            'minify_pps'          => false,
            'cache_pps_in_client' => false,
            'cache_pps_in_server' => false,
            'default_skin'        => '',
        ],
        'global_filters' => [
            'before' => [
                'browser_filter' => [],
            ],
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
                    'session_dsn'           => 'sqlite://' . swap\var_dir . '/session/session.db/user_session',
                ],
                'admin' => [
                    'sid_name'              => 'admin_sid',
                    'default_alive_seconds' => 3600,
                    'trace_last_active'     => false,
                    'session_dsn'           => 'sqlite://' . swap\var_dir . '/session/session.db/admin_session',
                ],
            ],
        ],
    ],
    'modules' => [
        'api' => [
            'url' => [
                'domains' => [],
            ],
        ],
        'admin' => [
            'url' => [
                'domains' => [],
            ],
        ],
    ],
];
