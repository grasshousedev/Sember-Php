<?php

return [
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
    ],

    // Debug
    'debug' => true,
];