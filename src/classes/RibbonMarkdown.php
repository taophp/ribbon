<?php

class RibbonMarkdown extends \cebe\markdown\GithubMarkdown {
    static protected $notLeetChars   = 'LetSpeak';
    static protected $leetChars      = '1375p34k';

    protected function identify1337($line, $lines, $current) {
        if (strncmp($line, '->1337', 6) === 0) {
            return true;
        }
        return false;
    }

    protected function consume1337($lines,$current) {
        $block = ['1337','content'=>[]];
        $current++;
        $max = count($lines);
        $line = $lines[$current];
        do {
            $block['content'][]=$line;
            $current++;
            $line = $lines[$current];
        } while ($line !=='<-1337' && $current < $max);
        return [$block,$current];        
    }
    
    protected function render1337($block) {
                return '<p class="leet">' . htmlspecialchars(implode("\n", static::translate1337($block['content'])) . "\n", ENT_NOQUOTES, 'UTF-8') . '</p>';
    }
    
    static protected function translate1337($lines) {
        foreach ($lines as $k => $line) {
            $lines[$k] = strtr($line,static::$notLeetChars,static::$leetChars);
        }
        return $lines;
    }
}
