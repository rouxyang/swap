<?php
return [
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
                // 'session_dsn'        => 'memcached://ip:port',
                // 'session_dsn'        => ['memcached://ip1:port1', 'memcached://ip2:port2'],
                // 'session_dsn'        => 'mysql://user:pass@ip:port/db_name/table_name',
                // 'session_dsn'        => 'pgsql://user:pass@ip:port/db_name/table_name',
                'session_dsn'           => 'sqlite://' . kern\run_dir . '/session/session.db/user_session',
            ],
        ],
    ],
];
