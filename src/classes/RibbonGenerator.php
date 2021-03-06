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
    static protected $postsSourceDirectory;
    
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


    public static function generate(bool $force = false) : bool {
        $indexFile = static::$container->settings['postDestinationDirectory'].'/index.html';
        $lastGeneration = filemtime($indexFile);
        
        if (!static::isIniated()) {
            throw new Exception('RibbonGenerator was not initiated!');
        }
        
        $view = new \Slim\Views\Twig(static::$container['settings']['twig']['templatePath'],static::$container['settings']['twig']['env']);
        $view->addExtension(new \Twig_Extension_Debug());

        $files = glob(static::$container->settings['postsSourceDirectory'].'/*md');
        
        $posts = [];
        foreach ($files as $file) {
            $post = new RibbonPost(static::$container);
            $post->createFromFile($file);
            if (!array_key_exists('updatedTo',$post->yaml)) {
                $post->year = $year = date('Y',strtotime($post->yaml['date']));
                $post->month = $month = date('m',strtotime($post->yaml['date']));
                $post->day = $day = date('j',strtotime($post->yaml['date']));
                $post->time = $time = date('H:i',strtotime($post->yaml['date']));
                $post->seconds = $seconds = date('s',strtotime($post->yaml['date']));

                $posts[$year][$month][$day][$time][$seconds] = $post;
            }
            if ($force === true || filemtime($file) > $lastGeneration) {
                file_put_contents(static::$container->settings['postDestinationDirectory'].'/'.$post->getHtmlFilename(),
                $view->fetch('post.html.twig',['post'=>$post,'frontConfig' => static::$container->settings['front']]));
            }
        }
        krsortRecursive($posts);
        
        return file_put_contents($indexFile,
                $view->fetch('index.html.twig',['posts'=>$posts,'frontConfig' => static::$container->settings['front']]));

    }
    
}

/**
 * @see https://gist.github.com/cdzombak/601849
 * @param type $array
 * @param type $sort_flags
 * @return boolean
 */
function krsortRecursive(&$array, $sort_flags = SORT_REGULAR) {
    if (!is_array($array)) return false;
    krsort($array, $sort_flags);
    foreach ($array as &$arr) {
        krsortRecursive($arr, $sort_flags);
    }
    return true;
}