<?php

namespace Tests\Coercion;

use RequestConverter\Coercion\StringCoercer;

class StringCoercerTest extends TypeCoercerTestCase
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
        $coercer = new StringCoercer();
        $this->assertTypeError($coercer->coerce($value, "string", [], $this->ctx));
    }

    public function testEverything()
    {
        $coercer = new StringCoercer();

        $this->assertConvertedValue("2", $coercer->coerce(2, "string", [], $this->ctx));
        $this->assertConvertedValue("foo", $coercer->coerce("foo", "string", [], $this->ctx));
        $this->assertConvertedValue("2.5", $coercer->coerce(2.5, "string", [], $this->ctx));
    }
}
