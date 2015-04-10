<?php

namespace CodeCloud;

use PhpParser\Lexer;
use PhpParser\Node;
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

        $this->analysed = [];
        foreach ($tokens as $node) {
            $this->processNode($node);
        }
        return $this->analysed;
    }

    /**
     * @param array $array
     */
    private function tallyArray(array $array) {
        foreach ($array as $part) {
            $this->tally($part);
        }
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
    private function processNode(Node $node = null) {
        if ($node === null) {
            return;
        }

        switch (get_class($node)) {
            case 'PhpParser\Node\Expr\Variable':
            case 'PhpParser\Node\Param':
            /** @var Node\Expr\Variable|Node\Param $node */
                $this->tally($node->name);
                break;

            case 'PhpParser\Node\Expr\Assign':
                /** @var Node\Expr\Assign $node */
                $this->processNode($node->var);
                $this->processNode($node->expr);
                break;

            case 'PhpParser\Node\Expr\BinaryOp\Identical':
                /** @var Node\Expr\BinaryOp\Identical $node */
                $this->processNode($node->left);
                $this->processNode($node->right);
                break;

            case 'PhpParser\Node\Scalar\String_':
            case 'PhpParser\Node\Scalar\LNumber':
            case 'PhpParser\Node\Scalar\DNumber':
            /**
             * @var Node\Scalar\String_|Node\Scalar\LNumber|Node\Scalar\DNumber $node
             */
                $this->tally((string) $node->value);
                break;

            case 'PhpParser\Node\Stmt\Namespace_':
            /** @var Node\Stmt\Namespace_ $node */
                foreach ($node->name as $part) {
                    $this->tallyArray($part);
                }
                foreach ($node->stmts as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Name':
            case 'PhpParser\Node\Name\FullyQualified':
                foreach ($node as $part) {
                    $this->tallyArray($part);
                }
                break;

            case 'PhpParser\Node\Expr\ConstFetch':
                foreach ($node->name as $part) {
                    $this->tallyArray($part);
                }
                break;

            case 'PhpParser\Node\Stmt\Property':
                foreach ($node->props as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Stmt\PropertyProperty':
                $this->tally($node->name);
                $this->processNode($node->default);
                break;

            case 'PhpParser\Node\Stmt\Use_':
                /**
                 * @var Node\Stmt\Use_   $node
                 * @var Node\Stmt\UseUse $part
                 */
                foreach ($node->uses as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Stmt\UseUse':
            /** @var Node\Stmt\UseUse $node */
                $this->processNode($node->name);
                $this->tally($node->alias);
                break;

            case 'PhpParser\Node\Stmt\Class_':
            /** @var Node\Stmt\Class_ $node */
                $this->tally($node->name);
                foreach ($node->stmts as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Expr\New_':
                /** @var Node\Expr\New_ $node */
                $this->processNode($node->class);
                foreach ($node->args as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Stmt\ClassMethod':
            /** @var Node\Stmt\ClassMethod $node */
                $this->tally($node->name);
                foreach ($node->params as $part) {
                    $this->processNode($part);
                }
                foreach ($node->stmts as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Stmt\Foreach_':
            /** @var Node\Stmt\Foreach_ $node */
                $this->processNode($node->expr);
                $this->processNode($node->keyVar);
                $this->processNode($node->valueVar);
                foreach ($node->stmts as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Stmt\If_':
            /** @var Node\Stmt\If_ $node */
                $this->processNode($node->cond);
                foreach ($node->stmts as $part) {
                    $this->processNode($part);
                }
                foreach ($node->elseifs as $part) {
                    $this->processNode($part);
                }
                $this->processNode($node->else);
                break;

            case 'PhpParser\Node\Arg':
            /** @var Node\Arg $node */
                $this->processNode($node->value);
                break;

            case 'PhpParser\Node\Stmt\Return_':
            case 'PhpParser\Node\Expr\BooleanNot':
            case 'PhpParser\Node\Expr\Cast\String_':
            case 'PhpParser\Node\Stmt\Throw_':
                $this->processNode($node->expr);
                break;

            case 'PhpParser\Node\Expr\MethodCall':
            /** @var Node\Expr\MethodCall $node */
                $this->processNode($node->var);
                $this->tally($node->name);
                foreach ($node->args as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Expr\FuncCall':
                /** @var Node\Expr\FuncCall $node */
                $this->processNode($node->name);
                foreach ($node->args as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Expr\ArrayDimFetch':
            /** @var Node\Expr\ArrayDimFetch $node */
                $this->processNode($node->var);
                $this->processNode($node->dim);
                break;

            case 'PhpParser\Node\Expr\PropertyFetch':
                /** @var Node\Expr\PropertyFetch $node */
                $this->processNode($node->var);
                $this->tally($node->name);
                break;

            case 'PhpParser\Node\Expr\PostInc':
                /** @var Node\Expr\PostInc $node */
                $this->processNode($node->var);
                break;

            case 'PhpParser\Node\Expr\Array_':
                /** @var Node\Expr\Array_ $node */
                foreach ($node->items as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Stmt\Switch_':
                /** @var Node\Stmt\Switch_ $node */
                $this->processNode($node->cond);
                foreach ($node->cases as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Stmt\Case_':
                /** @var Node\Stmt\Case_ $node */
                $this->processNode($node->cond);
                foreach ($node->stmts as $part) {
                    $this->processNode($part);
                }
                break;

            case 'PhpParser\Node\Stmt\Break_':
                break;

            default:
                throw new \Exception(var_export($node, true));
        }
    }

    private $analysed = [];
}
