<?php
return [
    'url' => [
        'domains'        => ['127.0.0.2:8080'],
        'is_https'       => false,
        'enable_rewrite' => false,
        'target_key'     => 't',
        'csrf_key'       => 's',
        'routes' => [
            '/about'    => 'site/about',
            '/login'    => 'site/login',
            '/register' => 'user/register',
        ],
    ],
    'view' => [
        'default_title'       => 'forum',
        'default_keywords'    => 'forum',
        'default_description' => 'This is forum.',
        'default_author'      => 'Jingcheng Zhang',
        'default_viewport'    => '',
        'default_skeleton'    => 'main',
        'minify_pps'          => false,
        'cache_pps_in_client' => false,
        'cache_pps_in_server' => false,
    ],
    'filter' => [
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
                'session_dsn'           => 'sqlite://' . kern\data_dir . '/sess/session.db/user_session',
            ],
            'admin' => [
                'sid_name'              => 'admin_sid',
                'default_alive_seconds' => 600,
                'trace_last_active'     => false,
                'session_dsn'           => 'sqlite://' . kern\data_dir . '/sess/session.db/admin_session',
            ],
        ],
    ],
];
