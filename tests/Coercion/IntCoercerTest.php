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
        ];
    }

    /**
     * @dataProvider incompatibleTypedValues
     */
    public function testIncompatibleTypesReturnError($value)
    {
        $coercer = new IntCoercer();
        $this->assertTypeError($coercer->coerce($value, "int", [], $this->ctx));
    }

    public function testInt()
    {
        $coercer = new IntCoercer();
        $this->assertConvertedValue(10, $coercer->coerce(10, "int", [], $this->ctx));
        $this->assertConvertedValue(-10, $coercer->coerce(-10, "int", [], $this->ctx));
    }

    public function testString()
    {
        $coercer = new IntCoercer();

        $this->assertConvertedValue(-10, $coercer->coerce("-10", "int", [], $this->ctx));
        $this->assertConvertedValue(10, $coercer->coerce("10", "int", [], $this->ctx));
        $this->assertConvertedValue(0, $coercer->coerce("0", "int", [], $this->ctx));
        $this->assertUncoercible($coercer->coerce("foo", "int", [], $this->ctx));
        $this->assertUncoercible($coercer->coerce("10.0", "int", [], $this->ctx));
        $this->assertUncoercible($coercer->coerce("10.45", "int", [], $this->ctx));
    }

    public function testFloat()
    {
        $coercer = new IntCoercer();

        $this->assertConvertedValue(456, $coercer->coerce(456.0, "int", [], $this->ctx));
        $this->assertConvertedValue(-99999, $coercer->coerce(-99999.0, "int", [], $this->ctx));
        $this->assertUncoercible($coercer->coerce(1.1, "int", [], $this->ctx));
    }
}
