<?php

namespace Codographic;

use PHPUnit_Framework_TestCase;


class DogfoodTest extends PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function analyserCanProcessItself_withoutExceptions() {
        $reflector = new \ReflectionClass('Codographic\Analyser');
        $filename  = $reflector->getFileName();
        $code      = file_get_contents($filename);

        $analyser  = new Analyser();
        $analyser->analyse($code);
    }
}
