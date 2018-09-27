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

$container['logger'] = function($container) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler($container['settings']['logFile']);
    $logger->pushHandler($file_handler);
    return $logger;
};

$app->add($config['authHandler']);

$container['view'] = function($container) {
    /** @see https://www.slimframework.com/docs/v3/features/templates.html#the-slimtwig-view-component */

    $view = new \Slim\Views\Twig($container['settings']['twig']['templatePath'],$container['settings']['twig']['env']);
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), rtrim($container->get('request')->getUri()->getBasePath())));
    return $view;
};

//$container['Twig'] = new Twig_Environment($config['twig']['loader'],$config['twig']['env']);

$app->get('/', function (Request $request, Response $response, array $args) {
    return $this->view->render($response,'newpost.html');
    $response->getBody()->write('Hi');

    return $response;
})->setName('newpost');

/*$app->get('/posts', function (Request $request, Response $response, array $args) {
    $response->getBody()->write('Hi');

    return $response;
});

$app->get('/posts/new', function (Request $request, Response $response, array $args) {
    $response->getBody()->write('Hi');

    return $response;
});

$app->get('/posts/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $response->getBody()->write('Edit post , '.$id);


    return $response;
}); */

$app->run();
