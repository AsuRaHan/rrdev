<?php defined('ROOT') or die('No direct script access.');
/**
 * Возможные значения database.driver
 *
 *      MariaDB :3306
 *      PostgreSQL :5432
 *      SQLite
 *      CUBRID
 *
 * подробнее смотрите описание
 * https://redbeanphp.com/index.php?p=/connection
 *
 * если не знаете что поставить просто поставьте значение 'database.driver' в SQLite
 * в этом случае все остальные поля будут проигнорированный
 *
 */

return [
    'database' => [
        'driver' => getenv('DB_DRIVER'),
        'host' => getenv('DB_HOST'),
        'port' => getenv('DB_PORT'),
        'username' => getenv('DB_USERNAME'),
        'password' => getenv('DB_PASSWORD'),
        'dbname' => getenv('DB_NAME'),
        'frozen' => getenv('DB_FROZEN')
    ],
    'email' => [
        'from' => 'noreply@example.com',
        'smtp' => 'smtp.example.com',
        'username' => 'username',
        'password' => 'password'
    ],
    'debug' => getenv('APP_DEBUG'),
    'app' => [
        'admin' => [
            'login' => 'admin',
            'password' => 'password123'
        ],
        'timezone'=> TIMEZONE
    ],
    'session' => [
        'prefix' => 'd_session_b',
        'driver' => 'file', // database или file но можно не указывать ничего то сессии будет обрабатывать сам php
        'directory' => SITE_DIR . 'usersessions',
        'gc_probability' => 30,
        'gc_divisor' => 100,
        'gc_maxlifetime' => 7200,
    ]
];