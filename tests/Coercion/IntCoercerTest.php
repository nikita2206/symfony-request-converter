<?php

namespace Tests\Coercion;

use RequestConverter\Coercion\IntCoercer;

class IntCoercerTest extends TypeCoercerTestCase
{
    public function incompatibleTypedValues()
    {
        return [
            [["nope"]],
            [false],
            [true],
            [new \stdClass()],
            [1.1]
        ];
    }

    /**
     * @dataProvider incompatibleTypedValues
     */
    public function testIncompatibleTypesReturnError($value)
    {
        $coercer = new IntCoercer();
        $this->assertTypeError($coercer->coerce($value, [], $this->ctx));
    }

    public function testInt()
    {
        $coercer = new IntCoercer();
        $this->assertConvertedValue(10, $coercer->coerce(10, [], $this->ctx));
        $this->assertConvertedValue(-10, $coercer->coerce(-10, [], $this->ctx));
    }

    public function testString()
    {
        $coercer = new IntCoercer();

        $this->assertConvertedValue(-10, $coercer->coerce("-10", [], $this->ctx));
        $this->assertConvertedValue(10, $coercer->coerce("10", [], $this->ctx));
        $this->assertConvertedValue(0, $coercer->coerce("0", [], $this->ctx));
        $this->assertUncoercible($coercer->coerce("foo", [], $this->ctx));
        $this->assertUncoercible($coercer->coerce("10.0", [], $this->ctx));
        $this->assertUncoercible($coercer->coerce("10.45", [], $this->ctx));
    }
}
