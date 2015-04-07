<?php

namespace CodeCloud;

use PHPUnit_Framework_TestCase;


class AnalyserTest extends PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function instantiate() {
        new Analyser();
    }
}
