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
        'PhpParser\Node\Stmt\Namespace_',
    ];

    const NODE_PROPERTIES = [
        'name',
        'value',
        'var',
        'expr',
        'stmts',
        'parts',
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
     * @param string $name
     */
    private function tally($name) {
        $name = (string) $name;
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
        foreach (self::NODE_PROPERTIES as $property) {
            if (isset($node->$property)) {
                $this->processOrTally($node->$property);
            }
        }
    }

    /**
     * @param Node|string $item
     */
    private function processOrTally($item) {
        if (is_array($item)) {
            foreach ($item as $subitem) {
                $this->processOrTally($subitem);
            }
        } else if ($item instanceof Node) {
            $this->processNode($item);
        } else {
            $this->tally($item);
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
