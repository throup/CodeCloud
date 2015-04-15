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
     */
    public function constant() {
        $analyser = new Analyser();

        $code = '<?php const THIS = "value";';

        $expected = [
            'THIS' => 1,
            'value' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function classConstant() {
        $analyser = new Analyser();

        $code = '<?php class example {const THIS = "value";}';

        $expected = [
            'example' => 1,
            'THIS' => 1,
            'value' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function emptyArray() {
        $analyser = new Analyser();

        $code = '<?php [];';

        $expected = [];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function simpleArray() {
        $analyser = new Analyser();

        $code = '<?php ["simple"];';

        $expected = [
            'simple' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function arrayWithKey() {
        $analyser = new Analyser();

        $code = '<?php ["key" => "simple"];';

        $expected = [
            'simple' => 1,
            'key'    => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function arrayWithNumericKey() {
        $analyser = new Analyser();

        $code = '<?php [0 => "simple"];';

        $expected = [
            'simple' => 1,
            '0'      => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function classWithSimpleMethod() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
class example {
    public function methodName() {}
}
END_PHP;

        $expected = [
            'example'    => 1,
            'methodName' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function classWithMethodAndUntypedParameters() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
class example {
    public function methodName(\$parameter, \$another) {}
}
END_PHP;

        $expected = [
            'example'    => 1,
            'methodName' => 1,
            'parameter'  => 1,
            'another'    => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function classWithMethodAndTypedParameters() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
class example {
    public function methodName(Bobby\\MyClass \$parameter, array \$another) {}
}
END_PHP;

        $expected = [
            'example'    => 1,
            'methodName' => 1,
            'parameter'  => 1,
            'another'    => 1,
            'Bobby'      => 1,
            'MyClass'    => 1,
            'array'      => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function classWithMethodAndDefaultParameters() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
class example {
    public function methodName(\$parameter = "value", \$another = null) {}
}
END_PHP;

        $expected = [
            'example'    => 1,
            'methodName' => 1,
            'parameter'  => 1,
            'another'    => 1,
            'value'      => 1,
            'null'       => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function simpleIf() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
if (\$value) {
}
END_PHP;

        $expected = [
            'value' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function ifWithElse() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
if (\$test) {
    \$thing = \$wotsit;
} else {
    \$good  = \$bad;
}
END_PHP;

        $expected = [
            'test'   => 1,
            'thing'  => 1,
            'wotsit' => 1,
            'good'   => 1,
            'bad'    => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function ifWithElseIfs() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
if (\$test) {
    \$thing = \$wotsit;
} else if (true) {
    \$thing = \$good;
} elseif (false) {
    \$thing = \$bad;
} else {
    \$good  = \$bad;
}
END_PHP;

        $expected = [
            'test'   => 1,
            'thing'  => 3,
            'wotsit' => 1,
            'good'   => 2,
            'bad'    => 2,
            'true'   => 1,
            'false'  => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function newObjectNoParams() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
new MyClass;
END_PHP;

        $expected = [
            'MyClass' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function newObjectWithParams() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
new MyClass(\$param, "another");
END_PHP;

        $expected = [
            'MyClass' => 1,
            'param'   => 1,
            'another' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function booleanNot() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
!\$this;
END_PHP;

        $expected = [
            'this' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function booleanOr() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one || \$another;
END_PHP;

        $expected = [
            'one' => 1,
            'another' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function booleanAnd() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one && \$another;
END_PHP;

        $expected = [
            'one' => 1,
            'another' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function alternativeOr() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one or \$another;
END_PHP;

        $expected = [
            'one' => 1,
            'another' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function alternativeAnd() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one and \$another;
END_PHP;

        $expected = [
            'one' => 1,
            'another' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function exclusiveOr() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one xor \$another;
END_PHP;

        $expected = [
            'one' => 1,
            'another' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function singleClassPropertyDeclaration() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
class Example {
    public \$property;
}
END_PHP;

        $expected = [
            'Example'  => 1,
            'property' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function multipleClassPropertyDeclaration() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
class Example {
    public \$property, \$another;
}
END_PHP;

        $expected = [
            'Example'  => 1,
            'property' => 1,
            'another'  => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function classPropertyDefinition() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
class Example {
    public \$property = "another";
}
END_PHP;

        $expected = [
            'Example'  => 1,
            'property' => 1,
            'another'  => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function classPropertyRead() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
class Example {
    public \$property = "another";

    public function some() {
        \$var = \$this->property;
    }
}
END_PHP;

        $expected = [
            'Example'  => 1,
            'property' => 2,
            'another'  => 1,
            'some'     => 1,
            'var'      => 1,
            'this'     => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function classPropertyWrite() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
class Example {
    public \$property;

    public function some() {
        \$this->property = "another";
    }
}
END_PHP;

        $expected = [
            'Example'  => 1,
            'property' => 2,
            'another'  => 1,
            'some'     => 1,
            'this'     => 1,
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
