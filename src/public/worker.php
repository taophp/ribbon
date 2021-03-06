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
    $files = glob('upload/*');
    $imgs = glob('img/*{png,jpg,gif,jpeg,JPEG,JPG}',GLOB_BRACE);
    return $this->view->render($response,'newpost.html.twig',[
        'messages' => $messages,
        'files' => $files,
        'imgs' => $imgs,
    ]);
})->setName('getnewpost');

$app->get('/l',function($request, Response $response){
    return $response->write('<script>'
            . 'window.top.location.href="../..";'
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
    $files = glob('upload/*');
    $imgs = glob('img/*{png,jpg,gif,jpeg,JPEG,JPG}',GLOB_BRACE);
    return $this->view->render($response,'newpost.html.twig',[
        'messages' => $messages,
        'files' => $files,
        'imgs' => $imgs,
        'textAreaContent' => $post->getTextAreaContent(),
        'currentView' => $post->getHtmlFilename(),
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

    $response = $response->withRedirect($this->router->pathFor('editpost',['filename' => $post->filename]),303);

    RibbonGenerator::init($this);
    RibbonGenerator::generate();

    return $response;
});

$app->get('/n/{filename}', function (Request $request, Response $response,$args){
    $messages = $this->flash->getMessages();
    $post = new RibbonPost($this);
    return $this->view->render($response,'newpost.html.twig',[
        'messages' => $messages,
    ]);
})->setName('addpart');

$app->post('/n/{filename}', function (Request $request, Response $response,$args){
    $data = $request->getParsedBody();
    $post = new RibbonPost($this);
    if ($post->createFromForm($data['content'],['previous' => $args['filename']]) && $post->save()) {
        $this->flash->addMessage('Success', 'The post was successfully saved.');
    }else{
        $this->flash->addMessage('Error', 'Impossible to save the post !');
    }

    $response = $response->withRedirect($this->router->pathFor('getnewpost'),303);

    RibbonGenerator::init($this);
    RibbonGenerator::generate();
    return $response;
});

$app->get('/g', function (Request $request, Response $response) {
    $params = $request->getQueryParams();
    $force = false;
    if (array_key_exists('f', $params) &&  $params['f'] === '1') {
        $force = true;
    }
    RibbonGenerator::init($this);
    RibbonGenerator::generate($force);
    
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $response = $response->withRedirect($_SERVER['HTTP_REFERER'],303);
    }
    return $response;
});


$app->post('/f',function (Request $request, Response $response) {
    $ph = new PluploadHandler(array(
            'target_dir' => 'upload/',
    ));

    $ph->sendNoCacheHeaders();
    $ph->sendCORSHeaders();

    if ($result = $ph->handleUpload()) {
            return $response->write(json_encode(array(
                    'OK' => 1,
                    'info' => $result
            )));
    } else {
            return $response->write(json_encode(array(
                    'OK' => 0,
                    'error' => array(
                            'code' => $ph->getErrorCode(),
                            'message' => $ph->getErrorMessage()
                    )
            )));
    }
});
$app->post('/i',function (Request $request, Response $response) {
    $ph = new PluploadHandler(array(
            'target_dir' => 'img/',
            'allow_extensions' => 'jpg,jpeg,png,gif,svg'
    ));

    $ph->sendNoCacheHeaders();
    $ph->sendCORSHeaders();

    if ($result = $ph->handleUpload()) {
            return $response->write(json_encode(array(
                    'OK' => 1,
                    'info' => $result
            )));
    } else {
            return $response->write(json_encode(array(
                    'OK' => 0,
                    'error' => array(
                            'code' => $ph->getErrorCode(),
                            'message' => $ph->getErrorMessage()
                    )
            )));
    }
});

$app->run();
