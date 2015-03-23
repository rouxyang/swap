<?php
return [
    'kern' => [
        'secret_key'        => 'DMSU5oFTedgvnN4rzjJ1XiCR7quxZLE8pl6aOK0c',
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
                'default_title'       => 'official',
                'default_keywords'    => 'official',
                'default_description' => 'This is official.',
                'default_author'      => 'Jingcheng Zhang',
                'default_viewport'    => '',
                'default_skeleton'    => 'main',
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
                        'trace_last_active'     => false,
                        'session_dsn'           => 'sqlite://' . kern\run_dir . '/session/session.db/user_session',
                    ],
                    'admin' => [
                        'sid_name'              => 'admin_sid',
                        'default_alive_seconds' => 600,
                        'trace_last_active'     => false,
                        'session_dsn'           => 'sqlite://' . kern\run_dir . '/session/session.db/admin_session',
                    ],
                ],
            ],
        ],
    ],
];
