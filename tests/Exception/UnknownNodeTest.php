<?php

namespace Codous\Exception;

use PHPUnit_Framework_TestCase;


class UnknownNodeTest extends PHPUnit_Framework_TestCase {
    /**
     * @test
     */
    public function instanceOfNamespaceException() {
        $instance = new UnknownNode();
        $this->assertInstanceOf('Codous\Exception', $instance);
    }

    /**
     * @test
     */
    public function createdWithNode_includesNodeNameInMessage() {
        $name = 'ThisIsAnArbitaryName';
        $node = $this->getMock('PhpParser\Node', [], [], $name);
        $exception = new UnknownNode($node);
        $this->assertContains($name, $exception->getMessage());
    }
}
