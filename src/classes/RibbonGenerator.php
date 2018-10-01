<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RibbonGenerator
 *
 * @author Stéphane Mourey <steph@stephanemourey.fr>
 */
class RibbonGenerator {
    static protected $container;
    static protected $postsSourceDirectory ;
    
    public static function init(Slim\Container $container) {
        static::$container = $container;
        $dir = $container->settings['postsSourceDirectory'];
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new Exception($dir.' MUST be writable directory !');
        }
        static::$postsSourceDirectory = $dir;        
    }
    
    protected static function isIniated() {
        return (static::$container !== null);
    }


    public static function generate() {
        if (!static::isIniated()) {
            throw new Exception('RibbonGenerator was not initiated!');
        }
        
        $files = glob(static::$container->settings['postsSourceDirectory'].'/*md');
        
        $posts = [];
        foreach ($files as $file) {
            $posts[] = new RibbonPostReader(static::$container,$file);
        }
        $view = new \Slim\Views\Twig(static::$container['settings']['twig']['templatePath'],static::$container['settings']['twig']['env']);
        //$view->addExtension(new Slim\Views\TwigExtension($container->get('router'), rtrim($container->get('request')->getUri()->getBasePath())));
        $view->addExtension(new \Twig_Extension_Debug());
        
        file_put_contents(static::$container->settings['postDestinationDirectory'].'/index.html', $view->fetch('index.html',['posts'=>$posts]));

    }
    
}
