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
    const DATE_FORMAT_4_YAML = 'Y-m-d H:i:s';
    const YAML_SEPARATOR = PHP_EOL.'---'.PHP_EOL;
    
    protected $postsSourceDirectory;
    protected $title;
    protected $content;
    //protected $fileName;
    //protected $yaml;
    //protected $content;
    //protected $timestamp;
    
    public function __construct(string $dir) {
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

    protected function _getFileName() : void {
        $this->fileName = date(static::DATE_FORMAT_4_FILE_NAME,$this->timestamp).urlencode($this->title).'.md';
    }
    
    protected function _getFileFullName() : void {
        $this->fileFullName = $this->postsSourceDirectory.'/'.$this->fileName = urlencode($this->title).'md';
    }
    
    protected function _getTimestamp()  {
        $this->timestamp = time();
    }

    protected function _getYaml() {
        $this->yaml = 'title: '.$this->title. PHP_EOL
                        . 'date: '.date(static::DATE_FORMAT_4_YAML,$this->timestamp);        
    }
    
    public function save(string $content) : void {
        /** @todo What if the file exists ? */
        $this->parseContentFromForm($content);
        file_put_contents($this->postsSourceDirectory.'/'.$this->fileName,
                          $this->yaml.static::YAML_SEPARATOR.$this->content
        );
    }
    
    protected function parseContentFromForm(string $content) : void {
        list ($this->title,$this->content) = explode(PHP_EOL,$content,2);
    }
    
    public function html() {
        $return = '<h1>'.$this->title.'</h1>';
        $return.= nl2br($this->content);
        return $return;
    }
}