<?php
return [
    'url' => [
        'domains'        => [],
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
    'filter' => [
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
];
