<?php

namespace Codographic;

use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\Parser;
use PhpParser\ParserAbstract;


class Analyser {
    const RECOGNISED_NODES = [
        'PhpParser\Node\Arg',
        'PhpParser\Node\Const_',
        'PhpParser\Node\Expr\Array_',
//        'PhpParser\Node\Expr\ArrayDimFetch',
        'PhpParser\Node\Expr\ArrayItem',
        'PhpParser\Node\Expr\Assign',
        'PhpParser\Node\Expr\BooleanNot',
        'PhpParser\Node\Expr\BinaryOp\BooleanAnd',
        'PhpParser\Node\Expr\BinaryOp\BooleanOr',
        'PhpParser\Node\Expr\BinaryOp\LogicalAnd',
        'PhpParser\Node\Expr\BinaryOp\LogicalOr',
        'PhpParser\Node\Expr\BinaryOp\LogicalXor',
//        'PhpParser\Node\Expr\Cast\String_',
//        'PhpParser\Node\Expr\ClassConstFetch',
        'PhpParser\Node\Expr\ConstFetch',
//        'PhpParser\Node\Expr\FuncCall',
//        'PhpParser\Node\Expr\Instanceof_',
//        'PhpParser\Node\Expr\Isset_',
//        'PhpParser\Node\Expr\MethodCall',
        'PhpParser\Node\Expr\New_',
//        'PhpParser\Node\Expr\PostInc',
        'PhpParser\Node\Expr\PropertyFetch',
        'PhpParser\Node\Expr\Variable',
        'PhpParser\Node\Name',
        'PhpParser\Node\Name\Relative',
        'PhpParser\Node\Param',
        'PhpParser\Node\Scalar\String_',
        'PhpParser\Node\Scalar\LNumber',
        'PhpParser\Node\Scalar\DNumber',
        'PhpParser\Node\Stmt\Class_',
        'PhpParser\Node\Stmt\ClassConst',
        'PhpParser\Node\Stmt\ClassMethod',
        'PhpParser\Node\Stmt\Const_',
        'PhpParser\Node\Stmt\Else_',
        'PhpParser\Node\Stmt\ElseIf_',
//        'PhpParser\Node\Stmt\Foreach_',
        'PhpParser\Node\Stmt\Global_',
        'PhpParser\Node\Stmt\If_',
        'PhpParser\Node\Stmt\Namespace_',
        'PhpParser\Node\Stmt\Property',
        'PhpParser\Node\Stmt\PropertyProperty',
//        'PhpParser\Node\Stmt\Return_',
//        'PhpParser\Node\Stmt\Throw_',
        'PhpParser\Node\Stmt\Use_',
        'PhpParser\Node\Stmt\UseUse',
    ];

    const NODE_PROPERTIES = [
        'name',  //
        'value', //
        'var',   //
        'vars',  //
        'expr',  //
        'stmts', //
        'parts', //
        'uses',
//        'type', //
        'extends',
        'implements',
        'consts',
        'items',
        'key',
//        'byRef', //
        'params',
//        'returnType',
//        'variadic', //
        'default',
        'cond',
        'elseifs',
        'else',
        'class',
        'args',
        'left',
        'right',
        'props',
//        'unpack',
//        'keyVar',
//        'valueVar',
//        'dim',
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
            $this->processNode($node);
        }
        return $this->analysed;
    }

    /**
     * @param string $name
     */
    private function tally($name) {
        $name = (string) $name;
        if ($name == '') {
            return;
        }

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
        $this->identifyNode($node);
        $this->specialCases($node);
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

    /**
     * @param Node $node
     */
    private function specialCases(Node $node) {
        if ($node instanceof Node\Stmt\UseUse) {
            // We only want to tally the alias if it is different
            // to the last part of the namespaced name.
            if ($node->alias != $node->name->getLast()) {
                $this->tally($node->alias);
            }
        }
        if ($node instanceof Node\Param) {
            // The type property appears to have different meanings
            // for different Node types; we only care about the version
            // for parameters.
            $this->processOrTally($node->type);
        }
    }
}
