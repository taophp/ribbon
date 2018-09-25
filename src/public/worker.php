<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../../vendor/autoload.php';

include_once('../../config/config.sample.php');
if (file_exists('../../config/config.php')) {
    include_once('../../config/config.php');
}

$app = new \Slim\App(['settings' => $config]);
$container = $app->getContainer();
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");

    return $response;
});
$app->run();
