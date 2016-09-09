<?php

namespace Tests\Coercion;

use RequestConverter\Coercion\BoolCoercer;
use RequestConverter\Validation\TypeError;

class BoolCoercerTest extends TypeCoercerTestCase
{
    public function incompatibleTypedValues()
    {
        return [
            [["true"]], // ["true"] is not a bool
            [new \stdClass()],
            [fopen('php://memory', 'w')]
        ];
    }

    /**
     * @dataProvider incompatibleTypedValues
     */
    public function testIncompatibleTypes($value)
    {
        $coercer = new BoolCoercer();

        $result = $coercer->coerce($value, "bool", [], $this->ctx);

        $this->assertTypeError($result);
    }

    public function testBool()
    {
        $coercer = new BoolCoercer();

        $result = $coercer->coerce(true, "bool", [], $this->ctx);
        $this->assertConvertedValue(true, $result);

        $result = $coercer->coerce(false, "bool", [], $this->ctx);
        $this->assertConvertedValue(false, $result);
    }

    public function testInt()
    {
        $coercer = new BoolCoercer();

        $this->assertConvertedValue(true, $coercer->coerce(1, "bool", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce(123, "bool", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce(-123, "bool", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce(0, "bool", [], $this->ctx));
    }

    public function testFloat()
    {
        $coercer = new BoolCoercer();

        $this->assertConvertedValue(false, $coercer->coerce(0.0, "bool", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce(0.000000001, "bool", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce(421.45, "bool", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce(-123.05, "bool", [], $this->ctx));
    }

    public function testString()
    {
        $coercer = new BoolCoercer();

        $this->assertConvertedValue(true, $coercer->coerce("foo", "bool", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("F", "bool", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("T", "bool", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("false", "bool", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("true", "bool", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("yes", "bool", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("no", "bool", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("Y", "bool", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("N", "bool", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("1", "bool", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("0", "bool", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("", "bool", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce(" ", "bool", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("-1", "bool", [], $this->ctx));
    }
}
