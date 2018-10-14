<?php

$config = [
    'errorLevel' => E_ALL,
    'front' => [
        'site' => [
            'title' => 'Ribbon',
            'motto' => 'a microbloging tool',
        ],
    ],
    'logFile' => __DIR__.'/../logs/app.log',
    'postsSourceDirectory' => __DIR__.'/../posts',
    'postDestinationDirectory' => __DIR__.'/../public',
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header
    'date_format' => 'Y-m-d',
    'time_format' => 'H:i',
    // Renderer settings
    'renderer' => [
        'template_path' => __DIR__ . '/../templates/',
    ],
    // Auth settings
    'authentificator' => function(){
        if (array_key_exists('PHP_AUTH_USER',$_SERVER) !== true
                || $_SERVER['PHP_AUTH_USER'] !== 'root'
                || $_SERVER['PHP_AUTH_PW']!=='t00r') {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            die('WTF ?!?');
        }
        return true;
    },
    // TWIG
    'twig' => [
        'templatePath' => __DIR__.'/../templates',
        'env' => [
            'debug' => true,
            'cache' => __DIR__.'/../templates_c',
        ]
    ]
];
