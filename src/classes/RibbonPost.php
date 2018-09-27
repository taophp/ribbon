<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ribbonPost
 *
 * @author Stephane Mourey <steph@stephanemourey.fr>
 */
class RibbonPost {
    const DATE_FORMAT_4_FILE_NAME = 'Y-m-d-';
    const DATE_FORM_4_YAML = 'Y-m-d H:i:s';
    
    protected $postsSourceDirectory;
    protected $fileName;
    protected $yaml;
    protected $content;
    protected $timestamp;
    protected $title;
    
    public function __construct(string $dir) {
        $this->postsSourceDirectory = $dir;
    }
    
    /*function __get($name) {
        if (!isset($this->$name)) {
            $method = '_get' . ucfirst($name);
            if (method_exists($this, $method))
                $this->$method();
            return $this->$name;
        }
    }*/

    public function save(string $content) : void {
        /** @todo What if the file already exists ? */
        $this->timestamp = time();
        $this->parseContent($content);
        file_put_contents($this->postsSourceDirectory.'/'.$this->fileName, $this->yaml.$this->content);
    }
    
    protected function _getTimestamp()  {
        $this->timestamp = time();
    }
    
    protected function parseContent(string $content) : void {
        list ($this->title,$this->content) = explode(PHP_EOL,$content,2);
    }
    
    protected function setFileName() {
        $this->fileName = date(static::DATE_FORMAT_4_FILE_NAME,$this->timestamp).urlencode(trim($this->title)).'.md';        
    }
    
    protected function setYaml() {
        $this->yaml = 'title: '.$this->title. PHP_EOL
                        . 'date: '.date(static::DATE_FORM_4_YAML,$this->timestamp). PHP_EOL
                        . '---'. PHP_EOL;        
    }
    
    public function html() {
        $return = '<h1>'.$this->title.'</h1>';
        $return.= nl2br($this->content);
        return $return;
    }
}
