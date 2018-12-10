<?php

class RibbonMarkdown extends \cebe\markdown\GithubMarkdown {
    static protected $char2leet = [
        'a' => ['a','4','4','/\\','@','/-\\','^','ä','ª','aye'],
        'b' => ['b','8','8','6','|3','ß','P>','|:'],
        'c' => ['c','c','[','¢','<','('],
        'd' => ['d','d','|)','o|','[)','I>','|>','þ'],
        'e' => ['e','3','3','&','£','ë','[-','€','ê','|=-'],
        'f' => ['f','f','|=','ph','|#'],
        'g' => ['g','6','6','&','(_+','9','C-','gee','(,'],
        'h' => ['h','h','#','/-/','[-]','{=}','<~>','|-|',']~[','}{',']-[','?','}-{'],
        'i' => ['i','!','!','1','|','&','eye','3y3','ï','][','¡'],
        'j' => ['j','j',',|','_|',';','_)'],
        'k' => ['k','X','X','|<','|{',']{','}<','|('],
        'l' => ['l','1','1','7','1_','|','|_','#','l'],
        'm' => ['m','m','//.','^^','|v|','[V]','{V}','|\\/|','/\\/\\','(u)','[]V[]','(V)'],
        'n' => ['n','n','//','^/','|\\|','/\\/','[\\]','','<\\>','{\\}','[]\\[]','n','/V','₪'],
        'o' => ['o','0','0','()','?p','','*','ö'],
        'p' => ['p','p','ph','|^','|*','|o','|^(o)','|>','|"','9','[]D','|?','|7'],
        'q' => ['q','9','9','(,)','<|','^(o)|','¶','O_'],
        'r' => ['r','r','|2','P\\','|?','|^','lz','[z','12','|2','Я'],
        's' => ['s','5','5','$','z','§','ehs'],
        't' => ['t','7','7','+','-|-','1','\'][\''],
        'u' => ['u','u','(_)','|_|','µ','v','ü'],
        'v' => ['v','v','\\/'],
        'w' => ['w','w','\\/\\/','vv','\'//','\\^/','(n)','\\V/','\\//','\\X/'],
        'x' => ['x','x','><','+','ecks',')(','Ж'],
        'y' => ['y','y','Y','\'/','`/','V/','\\-/','j','¥','%'],
        'z' => ['z','2','2','z','~\\_','~/_'],
    ];


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
        } while ($line !=='<-' && $current < $max);
        return [$block,$current];
    }

    protected function render1337($block) {
                return '<p class="leet">' . htmlspecialchars(implode("\n", static::translate1337($block['content'])) . "\n", ENT_NOQUOTES, 'UTF-8') . '</p>';
    }

    static protected function translate1337($lines) {
        foreach ($lines as $k => $line) {
           // $lines[$k] = strtr($line,static::$notLeetChars,static::$leetChars);
        }
        return $lines;
    }

    protected function identifyNote($line, $lines, $current) {
        if (strncmp($line, '->note', 7) === 0) {
            return true;
        }
        return false;
    }

    protected function consumeNote($lines,$current) {
        $block = ['note','content'=>[]];
        $current++;
        $max = count($lines);
        $line = $lines[$current];
        do {
            $block['content'][]=$line;
            $current++;
            $line = $lines[$current];
        } while ($line !=='<-' && $current < $max);
        return [$block,$current];
    }

    protected function renderNote($block) {
        return '<p class="note">' . implode('',$block['content']) . '</p>';
    }
}
