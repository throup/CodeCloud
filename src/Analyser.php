<?php

namespace CodeCloud;

use PhpParser\Lexer;
use PhpParser\Parser;


class Analyser {
    /**
     * @param  string      $code
     *
     * @return array|int[]
     */
    public function analyse($code) {
        $parser = new Parser(new Lexer);
        $tokens = $parser->parse($code);

        $analysed = [];
        foreach ($tokens as $node) {
            $name = $node->name;
            if (!array_key_exists($name, $analysed)) {
                $analysed[$name] = 0;
            }
            $analysed[$name]++;
        }
        return $analysed;
    }
}
