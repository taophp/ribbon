<?php

/**
 * Description of RibbonPostReader
 *
 * @author Stephane Mourey <steph@stephanemourey.fr>
 */

use Symfony\Component\Yaml\Yaml;

class RibbonPostReader {

    public $date;
    public $time;
    public $title;
    public $content;
    public $yaml;

    public function __construct(Slim\Container $container,string $fileName) {
        if (!is_file($fileName) || !is_readable($fileName)) {
            throw new Exception($fileName.' is not a file or is not readable');
        }
        $this->parse(file_get_contents($fileName));
    }

    function __get($name) {
        return $this->$name;
    }

    
    protected function parse(string $fileContent) {
        list ($yamlstring,$markdown) = explode(RibbonPostWritter::YAML_SEPARATOR,$fileContent,2);
        $this->yaml = Yaml::parse($yamlstring);
        $this->title = $this->yaml['title'];
        $mdParser = new \cebe\markdown\Markdown();
        $this->content = $mdParser->parse($markdown);
    }
}
