<?php

/**
 * Description of RibbonPostWritter
 *
 * @author Stephane Mourey <steph@stephanemourey.fr>
 */

use Symfony\Component\Yaml\Escaper;

class RibbonPostWritter {
    const DATE_FORMAT_4_FILE_NAME = 'Y-m-d-';
    const DATE_FORMAT_4_YAML = 'Y-m-d H:i:s';
    const YAML_SEPARATOR = PHP_EOL.'---'.PHP_EOL;
    
    protected $postsSourceDirectory;
    protected $title;
    protected $tags;
    protected $content;
    protected $app;
    
    //protected $fileName;
    //protected $yaml;
    //protected $content;
    //protected $timestamp;
    
    public function __construct(Slim\Container $container,string $filename = '') {
        $this->container = $container;
        $dir = $container->settings['postsSourceDirectory'];
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new Exception($dir.' MUST be writable directory !');
        }
        $this->postsSourceDirectory = $dir;
    }
    
    function __get($name) {
        if (!isset($this->$name)) {
            $method = '_get' . ucfirst($name);
            if (method_exists($this, $method)) {
                $this->$method();
            }
            return $this->$name;
        }
    }

    protected function _getFileName(int $postfix = null) : void {
        $this->fileName = date(static::DATE_FORMAT_4_FILE_NAME,$this->timestamp).urlencode(trim($this->title)).$postfix.'.md';
        if (file_exists($this->_getFileFullName())) {
            $this->_getFileName($postfix+1);
        }
    }
    
    protected function _getFileFullName() : string {
        $this->fileFullName = $this->postsSourceDirectory.'/'.$this->fileName;
        return $this->fileFullName;
    }
    
    protected function _getTimestamp()  {
        $this->timestamp = time();
    }

    protected function _getYaml() {
        $title = Escaper::requiresSingleQuoting($this->title)
                ? Escaper::escapeWithSingleQuotes($this->title)
                : (Escaper::requiresDoubleQuoting($this->title) ? Escaper::escapeWithDoubleQuotes($this->title) : $this->title);
        $this->yaml = 'title: '.$title. PHP_EOL
                        . 'date: '.date(static::DATE_FORMAT_4_YAML,$this->timestamp).PHP_EOL
                        . 'tags: '
                ;        
    }
    
    public function save(string $content) : bool {
        $this->parseContentFromForm($content);
        file_put_contents($this->postsSourceDirectory.'/'.$this->fileName,
                          $this->yaml.static::YAML_SEPARATOR.$this->content
        );
        RibbonGenerator::init($this->container);
        RibbonGenerator::generate();
        return true;
    }
    
    protected function parseContentFromForm(string $content) : void {
        list ($title,$this->content) = explode(PHP_EOL,$content,2);
        $break = strrpos($title,'[');
        $this->title = trim(substr($title,0,$break));
        $this->tags = substr(str_replace(' ','',$title),$break);
    }
    
    public function html() {
        $return = '<h1>'.$this->title.'</h1>';
        $return.= nl2br($this->content);
        return $return;
    }
}
