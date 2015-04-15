<?php

namespace Codographic;

use PHPUnit_Framework_TestCase;


class AnalyserTest extends PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function countsNothingInEmptyCode() {
        $analyser = new Analyser();

        $code = '';

        $expected = [];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function countsSingleVariable() {
        $analyser = new Analyser();

        $code = '<?php $variable;';

        $expected = [
            'variable' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function countsTwoInstancesOfSingleVariable() {
        $analyser = new Analyser();

        $code = '<?php $variable; $variable;';

        $expected = [
            'variable' => 2,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function countsTwoDifferentVariables() {
        $analyser = new Analyser();

        $code = '<?php $different; $variable;';

        $expected = [
            'different' => 1,
            'variable'  => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function assignOneVariableToAnother() {
        $analyser = new Analyser();

        $code = '<?php $different = $variable;';

        $expected = [
            'different' => 1,
            'variable'  => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function assignStringToAVariable() {
        $analyser = new Analyser();

        $code = '<?php $variable = "value";';

        $expected = [
            'variable'  => 1,
            'value'     => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function assignStringToVariableWithSameName() {
        $analyser = new Analyser();

        $code = '<?php $variable = "variable";';

        $expected = [
            'variable'  => 2,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function assignIntegerToAVariable() {
        $analyser = new Analyser();

        $code = '<?php $variable = 1;';

        $expected = [
            'variable'  => 1,
            '1'         => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function assignFloatToAVariable() {
        $analyser = new Analyser();

        $code = '<?php $variable = 1.2;';

        $expected = [
            'variable'  => 1,
            '1.2'       => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function singleNamespace() {
        $analyser = new Analyser();

        $code = '<?php namespace Example;';

        $expected = [
            'Example' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function twoNamespaces() {
        $analyser = new Analyser();

        $code = '<?php namespace Example {} namespace Another {}';

        $expected = [
            'Example' => 1,
            'Another' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function namespacesWithContent() {
        $analyser = new Analyser();

        $code = '<?php namespace Example { $variable; } namespace Another { $assignment = $variable; }';

        $expected = [
            'Example'    => 1,
            'Another'    => 1,
            'assignment' => 1,
            'variable'   => 2,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function singleGlobal() {
        $analyser = new Analyser();

        $code = '<?php global $variable;';

        $expected = [
            'variable' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function twoGlobals() {
        $analyser = new Analyser();

        $code = '<?php global $variable, $another;';

        $expected = [
            'another'  => 1,
            'variable' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function singleUse() {
        $analyser = new Analyser();

        $code = '<?php use Some\\Namespaced\\Thing;';

        $expected = [
            'Some'       => 1,
            'Namespaced' => 1,
            'Thing'      => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function twoUses() {
        $analyser = new Analyser();

        $code = '<?php use Some\\Namespaced\\Thing, Another\\Namespaced\\Thing;';

        $expected = [
            'Some'       => 1,
            'Namespaced' => 2,
            'Thing'      => 2,
            'Another'    => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function useWithAlias() {
        $analyser = new Analyser();

        $code = '<?php use Some\\Namespaced\\Thing as Another;';

        $expected = [
            'Some'       => 1,
            'Namespaced' => 1,
            'Thing'      => 1,
            'Another'    => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function useWithUnneccessaryAlias() {
        $analyser = new Analyser();

        $code = '<?php use Some\\Namespaced\\Thing as Thing;';

        $expected = [
            'Some'       => 1,
            'Namespaced' => 1,
            'Thing'      => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function simpleClass() {
        $analyser = new Analyser();

        $code = '<?php class simple {}';

        $expected = [
            'simple' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function extendingClass() {
        $analyser = new Analyser();

        $code = '<?php class simple extends another {}';

        $expected = [
            'simple' => 1,
            'another' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function implementingClass() {
        $analyser = new Analyser();

        $code = '<?php class simple implements another {}';

        $expected = [
            'simple' => 1,
            'another' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function complexClass() {
        $analyser = new Analyser();

        $code = '<?php abstract class complex extends bobble implements another, supplement {}';

        $expected = [
            'complex' => 1,
            'another' => 1,
            'bobble' => 1,
            'supplement' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     * @expectedException \Codographic\Exception\UnknownNode
     */
    public function throwsExceptionForUnknownParserNodes() {
        $unknownNode = $this->getMockForAbstractClass('PhpParser\Node');
        $code = 'Irrelevant';

        $prophet = $this->prophesize('PhpParser\ParserAbstract');
        $prophet->parse($code)->willReturn([$unknownNode]);
        $parser = $prophet->reveal();

        $analyser = new Analyser($parser);
        $analyser->analyse($code);
    }
}
