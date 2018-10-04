<?php

$config = [
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
    // Monolog settings
    'logger' => [
        'name' => 'slim-app',
        'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
        'level' => \Monolog\Logger::DEBUG,
    ],
    // Auth settings
    'authHandler' => new \Tuupola\Middleware\HttpBasicAuthentication( /** @see https://appelsiini.net/projects/slim-basic-auth/ */
        [
            'path' => '/',
            'secure' => false,
            'users' => [
                'root' => 't00r',
            ],
        ]
    ),
    // TWIG
    'twig' => [
        'templatePath' => __DIR__.'/../templates',
        'env' => [
            'debug' => true,
            'cache' => __DIR__.'/../templates_c',
        ]
    ]
];
