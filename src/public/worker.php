<?php
error_reporting(E_ALL);
session_start();
include_once('../config/config.sample.php');
if (file_exists('../config/config.php')) {
    include_once('../config/config.php');
}

error_reporting($config['errorLevel']);

$auth = $config['authentificator'];
if ($auth() !== true) {
    die('WTF ?!?');
}

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../vendor/autoload.php';


$app = new \Slim\App(['settings' => $config]);
$container = $app->getContainer();


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
    $this->logger->addInfo(__FILE__.':'.__LINE__.PHP_EOL);
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
