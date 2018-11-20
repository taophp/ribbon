<?php

class RibbonMarkdown extends \cebe\markdown\Markdown {

    protected function identify1337($line, $lines, $current) {
        // if a line starts with at least 3 backticks it is identified as a fenced code block
        if (strncmp($line, '->1337', 6) === 0) {
            return true;
        }
        return false;
    }

    protected function consume1337($lines,$current) {
        $txt=[];
        $current++;
        $line = $lines[$current];
        do {
            $txt[]=$line;
            $current++;
            $line = $lines[$current];
        } while ($line !=='<-1337');
        return [$txt,$current];        
    }
    
    protected function render1337($block) {
                return '<pre><code class="leet">' . htmlspecialchars(implode("\n", static::translate1337($block['content'])) . "\n", ENT_NOQUOTES, 'UTF-8') . '</code></pre>';
    }
    
    static protected function translate1337($str) {
        return $str;
    }
}
