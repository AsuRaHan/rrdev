<?php

defined('ROOT') OR die('No direct script access.');

return [
//    [
//        'path' => '/',
//        'controller' => 'IndexController',
//        'action' => 'index',
//    ],
    [
        'path' => '/users',
        'controller' => 'UserController',
        'action' => 'index',
    ],
    [
        'path' => '/users/(\d+)',
        'controller' => 'UserController',
        'action' => 'show',
        'params' => ['id'],
    ],
];
