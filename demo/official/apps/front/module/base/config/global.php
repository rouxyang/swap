<?php
return [
    'url' => [
        'domains'        => [],
        'is_https'       => false,
        'enable_rewrite' => false,
        'target_key'     => 't',
        'csrf_key'       => 's',
        'routes' => [
            '/about'         => 'site/about',
            '/download'      => 'site/download',
            '/demo'          => 'site/demo',
            '/documentation' => 'site/doc',
            '/support'       => 'site/support',
            '/license'       => 'site/license',
        ],
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
];
