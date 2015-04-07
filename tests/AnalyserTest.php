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
}
