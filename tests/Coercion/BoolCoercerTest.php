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

        $result = $coercer->coerce($value, [], $this->ctx);

        $this->assertTypeError($result);
    }

    public function testBool()
    {
        $coercer = new BoolCoercer();

        $result = $coercer->coerce(true, [], $this->ctx);
        $this->assertConvertedValue(true, $result);

        $result = $coercer->coerce(false, [], $this->ctx);
        $this->assertConvertedValue(false, $result);
    }

    public function testInt()
    {
        $coercer = new BoolCoercer();

        $this->assertConvertedValue(true, $coercer->coerce(1, [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce(123, [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce(-123, [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce(0, [], $this->ctx));
    }

    public function testFloat()
    {
        $coercer = new BoolCoercer();

        $this->assertConvertedValue(false, $coercer->coerce(0.0, [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce(0.000000001, [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce(421.45, [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce(-123.05, [], $this->ctx));
    }

    public function testString()
    {
        $coercer = new BoolCoercer();

        $this->assertConvertedValue(true, $coercer->coerce("foo", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("F", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("T", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("false", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("true", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("yes", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("no", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("Y", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("N", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("1", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("0", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce("", [], $this->ctx));
        $this->assertConvertedValue(false, $coercer->coerce(" ", [], $this->ctx));
        $this->assertConvertedValue(true, $coercer->coerce("-1", [], $this->ctx));
    }
}
