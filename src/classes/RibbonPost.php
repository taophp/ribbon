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
    const MORE_SEPARATOR = PHP_EOL.'--MORE--'.PHP_EOL;

    protected $yamlString;
    protected $markdownString;
    /**
     * @todo Twig seems to overprotect protected properties, so we cannot rely
     * on the magic getter, and the shortest path to access the two next properties
     * is to make them public : we will need a more secured way later. 
     */
    public $yaml;
    public $filename;
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
                $post = new RibbonPost($this->container);
                $post->createFromFile($v,['updatedTo' => $this->filename]);
                $post->save();
            },
            'updatedTo' => function($v){
                $this->yaml['updatedTo'] = $v;
            },
            'previous' => function($v){
                $this->yaml['previous'] = $v;
                $post = new RibbonPost($this->container);
                $post->createFromFile($v,['next' => $this->filename]);
                $post->save();
            },
            'next' => function($v){
                $this->yaml['next'] = $v;
            },
        ];
    }
    
    public function getHtmlFilename() : string {
        return str_replace('.md','.html',$this->filename);
    }
    
    public function createFromForm(string $content,$additionalParams = []) : bool {
        list ($title,$this->markdownString) = explode(PHP_EOL,$content,2);
        $break = strrpos($title,'(');
        $this->yaml['title'] = trim(substr($title,0,$break));
        $this->yaml['tags'] = explode(',',substr(trim(substr($title,$break)),1,-1));
        $this->yaml['date'] = date(static::DATE_FORMAT_4_YAML);
        $this->filename = date(static::DATE_FORMAT_4_FILE_NAME,time())
                .static::sanitizeString4filename(trim($this->yaml['title'])).'-'.time().'.md';
        
        if (is_array($additionalParams) && count($additionalParams)) {
            $this->additionalParsing($additionalParams);
        }
        return true;
    }
    
    public function createFromFile($filename,array $additionalParams = []) : bool {
        $this->filename = basename($filename);
        list ($this->yamlString,$this->markdownString) = explode(static::YAML_SEPARATOR,file_get_contents($this->postsSourceDirectory.'/'.$this->filename));
        $this->yaml = Yaml::parse($this->yamlString);
        foreach ($this->additionalParsing as $k => $v) {
            if (array_key_exists($k, $additionalParams)) {
                $v($additionalParams[$k]);
            }
        }
        return true;
    }
    
    protected function additionalParsing(array $additionalParams = []) : void {
        foreach ($this->additionalParsing as $k => $v) {
            if (array_key_exists($k, $additionalParams)) {
                $v($additionalParams[$k]);
            }
        }
    }
    
    public function save() {
        return file_put_contents($this->postsSourceDirectory.'/'.$this->filename,
                Yaml::dump($this->yaml).static::YAML_SEPARATOR.$this->markdownString);
    }
    
    public function getHtmlContent() : string {
        $mdParser = new \cebe\markdown\GithubMarkdown();
        $content = explode(RibbonPost::MORE_SEPARATOR,$this->markdownString,2)[0];
        return $mdParser->parse($content);
    }
    
    public function getHtmlMoreContent() {
        $mdParser = new \cebe\markdown\GithubMarkdown();
        $moreContent = @explode(RibbonPost::MORE_SEPARATOR,$this->markdownString,2)[1];
        return $mdParser->parse($moreContent);
        
    }
    public function getHtmlTitle() : string {
        $mdParser = new \cebe\markdown\GithubMarkdown();
        return $mdParser->parse($this->yaml['title']);
    }
    
    public function getTextAreaContent() : string {
        return $this->yaml['title']
                .' ('.implode(',',$this->yaml['tags']).')'
                . PHP_EOL . $this->markdownString;
    }
    
    public function thereIsMore () : bool {
        return strpos($this->markdownString,RibbonPost::MORE_SEPARATOR)!== false;
    }
    
    /**
     * Sanitize string to use as a filename
     * @see https://stackoverflow.com/questions/2021624/string-sanitizer-for-filename#answer-2021729
     * 
     * @param string $dangerousFilename unsecured string
     * @return string secured for filename string
     */
    public static function sanitizeString4filename(string $dangerousFilename) : string {
        return mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $dangerousFilename);
    }
    
}