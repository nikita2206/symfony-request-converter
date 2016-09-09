<?php

namespace Tests\Coercion;

use RequestConverter\Coercion\FloatCoercer;

class FloatCoercerTest extends TypeCoercerTestCase
{
    public function incompatibleTypedValues()
    {
        return [
            [["nope"]],
            [false],
            [true],
            [new \stdClass()]
        ];
    }

    /**
     * @dataProvider incompatibleTypedValues
     */
    public function testIncompatibleTypesReturnError($value)
    {
        $coercer = new FloatCoercer();
        $this->assertTypeError($coercer->coerce($value, "float", [], $this->ctx));
    }

    public function testSimple()
    {
        $coercer = new FloatCoercer();
        $this->assertConvertedValue(2.1, $coercer->coerce(2.1, "float", [], $this->ctx));
        $this->assertConvertedValue(-55646.12, $coercer->coerce(-55646.12, "float", [], $this->ctx));
        $this->assertConvertedValue(-55646., $coercer->coerce(-55646, "float", [], $this->ctx));
        $this->assertConvertedValue(123., $coercer->coerce(123, "float", [], $this->ctx));
    }

    public function testString()
    {
        $coercer = new FloatCoercer();

        $this->assertConvertedValue(123., $coercer->coerce("123", "float", [], $this->ctx));
        $this->assertConvertedValue(123.321, $coercer->coerce("123.321", "float", [], $this->ctx));
        $this->assertConvertedValue(-123., $coercer->coerce("-123", "float", [], $this->ctx));
        $this->assertConvertedValue(-999.999, $coercer->coerce("-999.999", "float", [], $this->ctx));
        $this->assertUncoercible($coercer->coerce("456 hehe", "float", [], $this->ctx));
        $this->assertUncoercible($coercer->coerce("nope", "float", [], $this->ctx));
    }
}
