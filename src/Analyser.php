<?php

namespace Codous;

use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\Parser;
use PhpParser\ParserAbstract;


class Analyser {
    public function __construct(ParserAbstract $parser = null) {
        if (!$parser) {
            $parser = new Parser(new Lexer);
        }
        $this->parser = $parser;
    }

    /**
     * @param  string      $code
     *
     * @return array|int[]
     */
    public function analyse($code) {
        $tokens = $this->parser->parse($code);

        $this->analysed = [];
        foreach ($tokens as $node) {
            $this->processNode($node);
        }
        return $this->analysed;
    }

    /**
     * @param $name
     */
    private function tally($name) {
        if (!array_key_exists($name, $this->analysed)) {
            $this->analysed[$name] = 0;
        }
        $this->analysed[$name]++;
    }

    /**
     * @param Node\Expr $node
     */
    private function processNode(Node $node) {
        switch (get_class($node)) {
            case 'PhpParser\Node\Expr\Variable':
            /** @var Node\Expr\Variable $node */
                $this->tally($node->name);
                break;

            case 'PhpParser\Node\Expr\Assign':
                /** @var Node\Expr\Assign $node */
                $this->processNode($node->var);
                $this->processNode($node->expr);
                break;

            case 'PhpParser\Node\Scalar\String_':
            case 'PhpParser\Node\Scalar\LNumber':
            case 'PhpParser\Node\Scalar\DNumber':
            /**
             * @var Node\Scalar\String_|Node\Scalar\LNumber|Node\Scalar\DNumber $node
             */
                $this->tally((string) $node->value);
                break;

            default:
                throw new Exception\UnknownNode(var_export($node, true));
        }
    }

    /**
     * @var array|int[]
     */
    private $analysed = [];

    /**
     * @var ParserAbstract
     */
    private $parser;
}
