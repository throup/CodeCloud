<?php

namespace CodeCloud;

use PHPUnit_Framework_TestCase;


class AcceptanceTest extends PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function acceptanceTest() {
        $analyser = new Analyser();

        $code = file_get_contents(__DIR__ . '/../src/Analyser.php');

        $expected = [
            'this'                                   => 55,
            'node'                                   => 54,
            'processNode'                            => 39,
            'part'                                   => 38,
            'name'                                   => 14,
            'tally'                                  => 10,
            'analysed'                               => 6,
            'stmts'                                  => 6,
            'var'                                    => 5,
            'tallyArray'                             => 4,
            'cond'                                   => 3,
            'PhpParser'                              => 3,
            'Lexer'                                  => 3,
            'expr'                                   => 3,
            'args'                                   => 3,
            'Parser'                                 => 3,
            'parser'                                 => 2,
            'array'                                  => 2,
            'tokens'                                 => 2,
            'value'                                  => 2,
            'code'                                   => 2,
            'Node'                                   => 2,
            'dim'                                    => 1,
            'elseifs'                                => 1,
            'else'                                   => 1,
            'default'                                => 1,
            'class'                                  => 1,
            'array_key_exists'                       => 1,
            'cases'                                  => 1,
            'parse'                                  => 1,
            'true'                                   => 1,
            'right'                                  => 1,
            'uses'                                   => 1,
            'valueVar'                               => 1,
            'var_export'                             => 1,
            'props'                                  => 1,
            'params'                                 => 1,
            'keyVar'                                 => 1,
            'items'                                  => 1,
            'left'                                   => 1,
            'null'                                   => 1,
            'get_class'                              => 1,
            'PhpParser\Node\Stmt\Use_'               => 1,
            'PhpParser\Node\Expr\New_'               => 1,
            'PhpParser\Node\Expr\MethodCall'         => 1,
            'PhpParser\Node\Expr\FuncCall'           => 1,
            'PhpParser\Node\Expr\PostInc'            => 1,
            'PhpParser\Node\Expr\PropertyFetch'      => 1,
            '0'                                      => 1,
            'PhpParser\Node\Name'                    => 1,
            'PhpParser\Node\Expr\Variable'           => 1,
            'PhpParser\Node\Expr\ConstFetch'         => 1,
            'PhpParser\Node\Expr\Cast\String_'       => 1,
            'PhpParser\Node\Arg'                     => 1,
            'Exception'                              => 1,
            'CodeCloud'                              => 1,
            'PhpParser\Node\Expr\ArrayDimFetch'      => 1,
            'PhpParser\Node\Expr\Array_'             => 1,
            'PhpParser\Node\Expr\BooleanNot'         => 1,
            'PhpParser\Node\Expr\BinaryOp\Identical' => 1,
            'PhpParser\Node\Expr\Assign'             => 1,
            'PhpParser\Node\Name\FullyQualified'     => 1,
            'PhpParser\Node\Param'                   => 1,
            'PhpParser\Node\Stmt\Return_'            => 1,
            'PhpParser\Node\Stmt\PropertyProperty'   => 1,
            'PhpParser\Node\Stmt\Property'           => 1,
            'PhpParser\Node\Stmt\Switch_'            => 1,
            'PhpParser\Node\Stmt\Throw_'             => 1,
            'alias'                                  => 1,
            'Analyser'                               => 1,
            'PhpParser\Node\Stmt\UseUse'             => 1,
            'PhpParser\Node\Stmt\Namespace_'         => 1,
            'PhpParser\Node\Stmt\If_'                => 1,
            'PhpParser\Node\Scalar\String_'          => 1,
            'PhpParser\Node\Scalar\LNumber'          => 1,
            'PhpParser\Node\Scalar\DNumber'          => 1,
            'PhpParser\Node\Stmt\Break_'             => 1,
            'PhpParser\Node\Stmt\Case_'              => 1,
            'PhpParser\Node\Stmt\Foreach_'           => 1,
            'PhpParser\Node\Stmt\Class_'             => 1,
            'PhpParser\Node\Stmt\ClassMethod'        => 1,
            'analyse'                                => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }
}
