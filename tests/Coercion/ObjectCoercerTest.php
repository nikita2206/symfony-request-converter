<?php

namespace Tests\Coercion;

use RequestConverter\Coercion\ObjectCoercer;
use RequestConverter\ConversionResult;

class ObjectCoercerTest extends TypeCoercerTestCase
{
    public function incompatibleTypedValues()
    {
        return [
            [false],
            [true],
            [new \stdClass()],
            [1],
            [1.4]
        ];
    }

    /**
     * @dataProvider incompatibleTypedValues
     */
    public function testIncompatibleTypesReturnError($value)
    {
        $coercer = new ObjectCoercer();
        $this->assertTypeError($coercer->coerce($value, [], $this->ctx));
    }

    public function testPassthrough()
    {
        $input = ["a" => 1, "b" => 2];
        $output = (object)$input;
        $coercer = new ObjectCoercer();

        $this->ctx->expects($this->once())->method("convert")
            ->with($input, $this->equalTo(new \ReflectionClass("stdClass")))
            ->willReturn(ConversionResult::value($output));

        $this->assertConvertedValue($output, $coercer->coerce($input, ["stdClass"], $this->ctx));
    }
}
