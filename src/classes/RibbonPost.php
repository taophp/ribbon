<?php
/**
 * Description of RibbonPostWritter
 *
 * @author Stephane Mourey <steph@stephanemourey.fr>
 */

use Symfony\Component\Yaml\Escaper;
use Symfony\Component\Yaml\Yaml;

class RibbonPost {
    const DATE_FORMAT_4_FILE_NAME = 'Y-m-d-';
    const DATE_FORMAT_4_YAML = 'Y-m-d H:i:s';
    const YAML_SEPARATOR = PHP_EOL.'---'.PHP_EOL;

    protected $yamlString;
    protected $markdownString;
    protected $yaml;
    protected $filename;
    protected $container;
    protected $postsSourceDirectory;
    
    protected $additionalParsing = [];
    
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->property;
        }
        return false;
    }
    
    public function __construct(Slim\Container $container) {
        $this->container = $container;
        $dir = $container->settings['postsSourceDirectory'];
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new Exception($dir.' MUST be writable directory!');
        }
        $this->postsSourceDirectory = $dir;
        
        $this->additionalParsing = [
            'updatedFrom' => function($v){
                $this->yaml['updatedFrom'] = $v;
            },
            'updatedTo' => function($v){
                $this->yaml['updatedTo'] = $v;
            },
        ];
    }
    
    public function createFromForm(string $content,$additionalParams = []) : bool {
        list ($title,$this->markdownString) = explode(PHP_EOL,$content,2);
        $break = strrpos($title,'(');
        $this->yaml['title'] = trim(substr($title,0,$break));
        $this->yaml['tags'] = substr(trim(substr($title,$break)),1,-1);
        $this->yaml['date'] = date(static::DATE_FORMAT_4_YAML);
        $this->filename = rawurlencode($this->yaml['title']).'.md';
        
        if (is_array($additionalParams) && count($additionalParams)) {
            $this->additionnalParsing($additionnalParams);
        }
        return true;
    }
    
    public function createFromFile($filename,array $additionalParams = []) : bool {
        return true;
    }
    
    protected function additionnalParsing(array $additionalParams = []) : void {
        foreach ($additionalParsing as $k => $v) {
            if (array_key_exists($k, $additionalParams)) {
                $v($additionalParams[$k]);
            }
        }
    }
    
    public function save() {
        file_put_contents($this->postsSourceDirectory.'/'.$this->filename,
                Yaml::dump($this->yaml).static::YAML_SEPARATOR.$this->markdownString);
        RibbonGenerator::init($this->container);
        RibbonGenerator::generate();        
    }
}