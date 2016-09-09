<?php

namespace Tests\Coercion;

use RequestConverter\Coercion\ArrayCoercer;
use RequestConverter\ConversionResult;
use RequestConverter\Validation\MissingFieldError;
use RequestConverter\Validation\TypeError;
use RequestConverter\Validation\UncoercibleValueError;

class ArrayCoercerTest extends TypeCoercerTestCase
{
    public function notArrays()
    {
        return [
            [0],
            [5.5],
            [false],
            ["array"],
            [new \stdClass()],
            [null]
        ];
    }

    /**
     * @dataProvider notArrays
     */
    public function testNotArray($value)
    {
        $coercer = new ArrayCoercer();
        $result = $coercer->coerce($value, "array", [], $this->ctx);

        $this->assertTypeError($result);
    }

    public function testNonParametrized()
    {
        $value = [1, 2, 6 => 3];

        $coercer = new ArrayCoercer();
        $result = $coercer->coerce($value, "array", [], $this->ctx);

        $this->assertConvertedValue([1, 2, 3], $result);
    }

    public function testParametrized()
    {
        $value = [1, 2, 3];

        $coercer = new ArrayCoercer();

        $this->ctx->expects($this->exactly(3))->method("coerce")
            ->withConsecutive(
                [1, "int"],
                [2, "int"],
                [3, "int"])
            ->willReturnOnConsecutiveCalls(
                ConversionResult::value(1),
                ConversionResult::value(2),
                ConversionResult::value(3));

        $result = $coercer->coerce($value, "array<int>", ["int"], $this->ctx);

        $this->assertConvertedValue($value, $result);
    }

    public function testErrors()
    {
        $coercer = new ArrayCoercer();

        $this->ctx->expects($this->exactly(3))->method("coerce")
            ->withConsecutive(
                [1, "int"],
                [2, "int"],
                [3, "int"])
            ->willReturnOnConsecutiveCalls(
                ConversionResult::value(1),
                ConversionResult::error(new MissingFieldError(), 2),
                ConversionResult::error(new UncoercibleValueError("string", "array"), 3));

        $result = $coercer->coerce([1, 2, 3], "array", ["int"], $this->ctx);

        $this->assertSame([1, 2, 3], $result->getValue());
        $this->assertCount(2, $result->getErrors());
        $this->assertInstanceOf(MissingFieldError::class, $result->getErrors()[0]);
        $this->assertSame("[1]", $result->getErrors()[0]->getField());
        $this->assertInstanceOf(UncoercibleValueError::class, $result->getErrors()[1]);
        $this->assertSame("[2]", $result->getErrors()[1]->getField());
    }

    public function testTerminalError()
    {
        $coercer = new ArrayCoercer();

        $this->ctx->expects($this->exactly(2))->method("coerce")
            ->withConsecutive(
                [1, "int"],
                [2, "int"])
            ->willReturnOnConsecutiveCalls(
                ConversionResult::value(1),
                ConversionResult::error(new MissingFieldError()));

        $result = $coercer->coerce([1, 2, 3], "array<int>", ["int"], $this->ctx);

        $this->assertNull($result->getValue());
        $this->assertCount(1, $result->getErrors());
        $this->assertInstanceOf(MissingFieldError::class, $result->getErrors()[0]);
        $this->assertSame("[1]", $result->getErrors()[0]->getField());
    }
}
