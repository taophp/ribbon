<?php
session_start();

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../vendor/autoload.php';

include_once('../config/config.sample.php');
if (file_exists('../config/config.php')) {
    include_once('../config/config.php');
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

$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

$container['view'] = function($container) {
    /** @see https://www.slimframework.com/docs/v3/features/templates.html#the-slimtwig-view-component */

    $view = new \Slim\Views\Twig($container['settings']['twig']['templatePath'],$container['settings']['twig']['env']);
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), rtrim($container->get('request')->getUri()->getBasePath())));
    $view->addExtension(new \Twig_Extension_Debug());
    
    return $view;
};

$app->get('/w', function (Request $request, Response $response) {
    $messages = $this->flash->getMessages();
    return $this->view->render($response,'newpost.html.twig',[
        'messages' => $messages,
    ]);
})->setName('getnewpost');

$app->post('/w', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $post = new RibbonPostWritter($this);
    if ($post->save($data['content'])) {
        $this->flash->addMessage('Success', 'The post was successfully saved.');
    }else{
        $this->flash->addMessage('Error', 'Impossible to save the post !');
    }

    $response = $response->withRedirect($this->router->pathFor('getnewpost'),303);

    return $response;
});

$app->run();
