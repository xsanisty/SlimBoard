<?php

$config['database'] = array(
    'default'       => 'sqlite',

    'connections'   => array(

        'mysql' => array(
            'driver'    => 'mysql',
            'host'      => isset($_SERVER['DB1_HOST']) ? $_SERVER['DB1_HOST'] : 'localhost',
            'database'  => isset($_SERVER['DB1_NAME']) ? $_SERVER['DB1_NAME'] : 'database',
            'username'  => isset($_SERVER['DB1_USER']) ? $_SERVER['DB1_USER'] : 'root',
            'password'  => isset($_SERVER['DB1_PASS']) ? $_SERVER['DB1_PASS'] : '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ),

        'sqlite' => array(
            'driver'   => 'sqlite',
            'database' => APP_PATH.'storage/db/database.sqlite',
            'prefix'   => '',
        ),

        'pgsql' => array(
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'database' => 'database',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ),

        'sqlsrv' => array(
            'driver'   => 'sqlsrv',
            'host'     => '127.0.0.1',
            'database' => 'database',
            'username' => 'user',
            'password' => '',
            'prefix'   => '',
        ),

    )
);