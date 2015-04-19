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
    public function useClassConstant() {
        $analyser = new Analyser();

        $code = <<< 'END_PHP'
<?php
class example {
    const THIS = "value";
}

$object = new example;
$object::THIS;
END_PHP;

        $expected = [
            'example' => 2,
            'THIS'    => 2,
            'value'   => 1,
            'object'  => 2,
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
    public function callClassMethodWithNoParameters() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
class example {
    public function methodName(\$parameter) {}
}

\$object = new example();
\$object->methodName();
END_PHP;

        $expected = [
            'example'    => 2,
            'methodName' => 2,
            'parameter'  => 1,
            'object'     => 2,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function callClassMethodWithParameter() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
class example {
    public function methodName(\$parameter) {}
}

\$object = new example();
\$object->methodName('hello');
END_PHP;

        $expected = [
            'example'    => 2,
            'methodName' => 2,
            'parameter'  => 1,
            'object'     => 2,
            'hello'      => 1,
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
    public function logicalEqual() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one == \$another;
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
    public function logicalIdentical() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one === \$another;
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
    public function logicalNotEqual() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one != \$another;
\$one <> \$another;
END_PHP;

        $expected = [
            'one'     => 2,
            'another' => 2,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function logicalNotIdentical() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one !== \$another;
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
    public function logicalGreaterThan() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one > \$another;
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
    public function logicalGreaterThanOrEqual() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one >= \$another;
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
    public function logicalLessThan() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one < \$another;
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
    public function logicalLessThanOrEqual() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$one <= \$another;
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
     */
    public function forEachWithNoKey() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
foreach (\$array as \$value) {}
END_PHP;

        $expected = [
            'array' => 1,
            'value' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function forEachWithKey() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
foreach (\$array as \$key => \$value) {}
END_PHP;

        $expected = [
            'array' => 1,
            'key'   => 1,
            'value' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function simpleFunction() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
function foo() {}
END_PHP;

        $expected = [
            'foo' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function simpleFunctionWithTypedParameter() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
function foo(DateTime \$time) {}
END_PHP;

        $expected = [
            'foo'      => 1,
            'DateTime' => 1,
            'time'     => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function simpleFunctionWithReturn() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
function foo() {
    return 'value';
}
END_PHP;

        $expected = [
            'foo'   => 1,
            'value' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function functionCallNoParameters() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
foo();
END_PHP;

        $expected = [
            'foo' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function functionCallWithParameter() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
foo('bar');
END_PHP;

        $expected = [
            'foo' => 1,
            'bar' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function postIncrement() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$var++;
END_PHP;

        $expected = [
            'var' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function postDecrement() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$var--;
END_PHP;

        $expected = [
            'var' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function preIncrement() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
++\$var;
END_PHP;

        $expected = [
            'var' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function preDecrement() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
--\$var;
END_PHP;

        $expected = [
            'var' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function throwAnException() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
throw new Exception();
END_PHP;

        $expected = [
            'Exception' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function castAsString() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
(string) \$variable;
END_PHP;

        $expected = [
            'variable' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function castAsInteger() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
(int)     \$variable;
(integer) \$variable;
END_PHP;

        $expected = [
            'variable' => 2,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function castAsFloat() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
(float)  \$variable;
(double) \$variable;
(real)   \$variable;
END_PHP;

        $expected = [
            'variable' => 3,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function castAsBoolean() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
(bool)    \$variable;
(boolean) \$variable;
END_PHP;

        $expected = [
            'variable' => 2,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function castAsArray() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
(array) \$variable;
END_PHP;

        $expected = [
            'variable' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function castAsObject() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
(object) \$variable;
END_PHP;

        $expected = [
            'variable' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function castAsBinaryString() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
(binary) \$variable;
b"Binary";
END_PHP;

        $expected = [
            'variable' => 1,
            'Binary'   => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function arrayRead() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$a = \$array[\$key];
END_PHP;

        $expected = [
            'a'     => 1,
            'array' => 1,
            'key'   => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function arrayWrite() {
        $analyser = new Analyser();

        $code = <<< END_PHP
<?php
\$array[\$key] = \$a;
END_PHP;

        $expected = [
            'a'     => 1,
            'array' => 1,
            'key'   => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function callToIsset() {
        $analyser = new Analyser();

        $code = <<< 'END_PHP'
<?php
isset($a);
END_PHP;

        $expected = [
            'a'     => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function callToInstanceof() {
        $analyser = new Analyser();

        $code = <<< 'END_PHP'
<?php
$a instanceof MyClass;
END_PHP;

        $expected = [
            'a'       => 1,
            'MyClass' => 1,
        ];

        $this->assertEquals($expected, $analyser->analyse($code));
    }

    /**
     * @test
     */
    public function stringWillBeTalliedInParts() {
        $analyser = new Analyser();

        $code = <<< 'END_PHP'
<?php
'This is a string of parts.';
END_PHP;

        $expected = [
            'This'   => 1,
            'is'     => 1,
            'a'      => 1,
            'string' => 1,
            'of'     => 1,
            'parts'  => 1,
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
