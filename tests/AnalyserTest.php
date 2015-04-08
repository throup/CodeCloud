<?php

namespace CodeCloud;

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
}
