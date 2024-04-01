<?php

return [
    // Post types
    'post_types' => [
        'post' => [
            'name' => 'Posts',
            'name_singular' => 'Post',
            'icon' => 'fas fa-list-ol',
            'table' => [
                'columns' => [
                    'title' => 'Title',
                    'status' => 'Status',
                    'published_at' => 'Published At',
                    'created_at' => 'Created At',
                    'views' => 'Views',
                ]
            ]
        ],
        'page' => [
            'name' => 'Pages',
            'name_singular' => 'Page',
            'icon' => 'fas fa-file-alt',
            'table' => [
                'columns' => [
                    'title' => 'Title',
                    'views' => 'Views',
                ]
            ]
        ],
    ],

    // SQLite
    'database_driver' => 'sqlite',
    'sqlite_path' => SEMBER_ROOT . '/storage/database.sqlite',

    // MySQL
//    'database_driver' => 'mysql',
//    'mysql_hostname' => 'localhost',
//    'mysql_database' => 'sember',
//    'mysql_username' => '',
//    'mysql_password' => '',

    // PostgreSQL
//    'database_driver' => 'pgsql',
//    'pgsql_hostname' => 'localhost',
//    'pgsql_database' => 'sember',
//    'pgsql_username' => '',
//    'pgsql_password' => '',

    // Migrations
    'migrations' => [
        \Sember\System\Migrations\AddViewsColumnToPostsTable::class,
        \Sember\System\Migrations\AddTypeColumnToPostsTable::class,
    ],

    // Debug
    'debug' => true,
];