<?php

$config = [
    'logFile' => __DIR__.'/../logs/app.log',
    'postsSourceDirectory' => __DIR__.'/../posts',
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header
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