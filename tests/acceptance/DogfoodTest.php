<?php

namespace Codographic;

use PHPUnit_Framework_TestCase;
use ReflectionClass;


/**
 * As a constantly-evolving language, it is inevitable that not all language
 * features will be correctly analysed by this tool.
 *
 * As a minimum level of compatibility, we need to ensure that this tool can
 * correctly analyse all language features used within its own source code.
 * Hence, this "dogfood" test.
 *
 * @see http://en.wikipedia.org/wiki/Eating_your_own_dog_food
 */
class DogfoodTest extends PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function analyserCanProcessItself_withoutExceptions() {
        $reflector = new ReflectionClass('Codographic\Analyser');
        $filename  = $reflector->getFileName();
        $code      = file_get_contents($filename);

        $analyser  = new Analyser();
        $this->assertEquals([], $analyser->analyse($code));
    }
}
