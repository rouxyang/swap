<?php
return [
    'kern' => [
        'secret_key'        => 'M01BXwRghOWp4Ksjq5GnJT8Co2kNUylDESv6QuIi',
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
    'modules' => [
        'base' => [
            'url' => [
                'domains'        => ['127.0.0.1:8080'],
                'is_https'       => false,
                'enable_rewrite' => false,
                'target_key'     => 't',
                'csrf_key'       => 's',
                'routes' => [
                    '/login'             => 'site/login',
                    '/about'             => 'site/about',
                    '/page/*'            => 'post/index?page=$1',
                    '/post/tag/*/page/*' => 'post/index?tag=$1&page=$2',
                    '/post/tag/*'        => 'post/index?tag=$1',
                    '/post/category/*'   => 'post/index?category_id=$1',
                    '/post/id/*'         => 'post/show?id=$1',
                    '/post/*/page/*'     => 'post/show?id=$1&page=$2',
                ],
            ],
            'view' => [
                'default_title'       => 'blog',
                'default_keywords'    => 'blog',
                'default_description' => 'This is blog.',
                'default_author'      => 'Jingcheng Zhang',
                'default_viewport'    => '',
                'default_skeleton'    => 'main',
                'minify_pps'          => false,
                'cache_pps_in_client' => false,
                'cache_pps_in_server' => false,
            ],
            'global_filters' => [
                'before' => [
                    'browser_filter' => ['ie6', 'ie7'],
                ],
                'after'  => [],
            ],
            'visitor' => [
                'cookie_domain' => '',
                'roles' => [
                    'member' => [
                        'sid_name'              => 'member_sid',
                        'default_alive_seconds' => 3600,
                        'trace_last_active'     => false,
                        'session_dsn'           => 'sqlite://' . kern\run_dir . '/session/session.db/member_session',
                    ],
                ],
            ],
        ],
    ],
];
