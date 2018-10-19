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

if (array_key_exists('logged', $_COOKIE) === false || $_COOKIE['logged'] !== true) {
    setcookie('logged',true,0,'/');
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

$app->get('/l',function($request, Response $response){
    return $response->write('<script>'
            . 'window.top.location.href="/";'
            . '</script>'
    );
});

$app->post('/w', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $post = new RibbonPost($this);
    if ($post->createFromForm($data['content']) && $post->save()) {
        $this->flash->addMessage('Success', 'The post was successfully saved.');
    }else{
        $this->flash->addMessage('Error', 'Impossible to save the post !');
    }

    $response = $response->withRedirect($this->router->pathFor('getnewpost'),303);

    RibbonGenerator::init($this);
    RibbonGenerator::generate();        

    return $response;
});

$app->get('/u/{filename}', function (Request $request, Response $response,$args) {
    $messages = $this->flash->getMessages();
    $post = new RibbonPost($this);
    $post->createFromFile($args['filename']);
    return $this->view->render($response,'newpost.html.twig',[
        'messages' => $messages,
        'textAreaContent' => $post->getTextAreaContent(),
    ]);
})->setName('editpost');

$app->post('/u/{filename}', function (Request $request, Response $response,$args) {
    $data = $request->getParsedBody();
    $post = new RibbonPost($this);
    if ($post->createFromForm($data['content'],['updatedFrom' => $args['filename']]) && $post->save()) {
        $this->flash->addMessage('Success', 'The post was successfully saved.');
    }else{
        $this->flash->addMessage('Error', 'Impossible to save the post !');
    }

    $response = $response->withRedirect($this->router->pathFor('getnewpost'),303);
    
    RibbonGenerator::init($this);
    RibbonGenerator::generate();        

    return $response;
});


$app->run();
