<?php

namespace Codographic;

use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\Parser;
use PhpParser\ParserAbstract;


class Analyser {
    const RECOGNISED_NODES = [
        'PhpParser\Node\Expr\Variable',
        'PhpParser\Node\Expr\Assign',
        'PhpParser\Node\Scalar\String_',
        'PhpParser\Node\Scalar\LNumber',
        'PhpParser\Node\Scalar\DNumber',
    ];

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
     * @throws Exception\UnknownNode
     */
    public function analyse($code) {
        $tokens = $this->parser->parse($code);

        $this->analysed = [];
        foreach ($tokens as $node) {
            $this->identifyNode($node);
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
     * @param  Node $node
     * @throws Exception\UnknownNode
     */
    private function identifyNode(Node $node) {
        if (!$this->isRecognised($node)) {
            throw new Exception\UnknownNode($node);
        }
    }

    /**
     * @param  Node $node
     * @return bool
     */
    private function isRecognised(Node $node) {
        return in_array(get_class($node), self::RECOGNISED_NODES);
    }

    /**
     * @param Node\Expr $node
     */
    private function processNode(Node $node) {
        if (isset($node->name)) {
            $this->tally($node->name);
        }
        if (isset($node->value)) {
            $this->tally((string) $node->value);
        }
        if (isset($node->var)) {
            $this->processNode($node->var);
        }
        if (isset($node->expr)) {
            $this->processNode($node->expr);
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

