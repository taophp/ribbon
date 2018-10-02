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
        $monthNames = ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
        foreach ($files as $file) {
            $post = new RibbonPostReader(static::$container,$file);
            $year = date('Y',$post->date);
            $month = date('m',$post->date);
            $day= date('d',$post->date);
            $time= date('H:i',$post->date);
            
            $posts[$year][$month][$day][$time] = $post;
        }
        ksortRecursive($posts);
        $view = new \Slim\Views\Twig(static::$container['settings']['twig']['templatePath'],static::$container['settings']['twig']['env']);
        //$view->addExtension(new Slim\Views\TwigExtension($container->get('router'), rtrim($container->get('request')->getUri()->getBasePath())));
        $view->addExtension(new \Twig_Extension_Debug());
        
        
        file_put_contents(static::$container->settings['postDestinationDirectory'].'/index.html', $view->fetch('index.html',['posts'=>$posts,'monthNames'=>$monthNames]));

    }
    
}

/**
 * @see https://gist.github.com/cdzombak/601849
 * @param type $array
 * @param type $sort_flags
 * @return boolean
 */
function ksortRecursive(&$array, $sort_flags = SORT_REGULAR) {
    if (!is_array($array)) return false;
    ksort($array, $sort_flags);
    foreach ($array as &$arr) {
        ksortRecursive($arr, $sort_flags);
    }
    return true;
}