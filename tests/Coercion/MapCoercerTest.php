<?php

namespace Tests\Coercion;

use RequestConverter\Coercion\MapCoercer;
use RequestConverter\ConversionResult;
use RequestConverter\Validation\MissingFieldError;
use RequestConverter\Validation\TypeError;

class MapCoercerTest extends TypeCoercerTestCase
{
    public function incompatibleTypedValues()
    {
        return [
            [false],
            [true],
            [new \stdClass()],
            [1.1],
            [1]
        ];
    }

    /**
     * @dataProvider incompatibleTypedValues
     */
    public function testIncompatibleTypesReturnError($value)
    {
        $coercer = new MapCoercer();
        $this->assertTypeError($coercer->coerce($value, "map", [], $this->ctx));
    }

    public function testNoParameters()
    {
        $coercer = new MapCoercer();

        $this->assertConvertedValue(["a" => 1, "b" => 9], $coercer->coerce(["a" => 1, "b" => 9], "map", [], $this->ctx));
    }

    public function testTypedValueOnly()
    {
        $coercer = new MapCoercer();

        $this->ctx->expects($this->exactly(3))->method("coerce")
            ->withConsecutive(
                [2, "int"],
                [6, "int"],
                [9, "int"])
            ->willReturnOnConsecutiveCalls(
                ConversionResult::value(2),
                ConversionResult::value(6),
                ConversionResult::value(9));
        $this->assertConvertedValue(["a" => 2, "b" => 6, "c" => 9],
            $coercer->coerce(["a" => 2, "b" => 6, "c" => 9], "map<int>", ["int"], $this->ctx));
    }

    public function testTypedKeyValue()
    {
        $coercer = new MapCoercer();

        $this->ctx->expects($this->exactly(4))->method("coerce")
            ->withConsecutive(
                ["a", "string"],
                [0, "int"],
                ["b", "string"],
                [1, "int"])
            ->willReturnOnConsecutiveCalls(
                ConversionResult::value("a"),
                ConversionResult::value(0),
                ConversionResult::value("b"),
                ConversionResult::value(1));

        $this->assertConvertedValue(["a", "b"], $coercer->coerce(["a", "b"], "map<int, string>", ["int", "string"], $this->ctx));
    }

    public function testTypedValueNonTerminalErrors()
    {
        $coercer = new MapCoercer();

        $this->ctx->expects($this->exactly(4))->method("coerce")
            ->withConsecutive(
                [2, "int"],
                [6, "int"],
                [9, "int"],
                [15, "int"])
            ->willReturnOnConsecutiveCalls(
                ConversionResult::value(2),
                ConversionResult::error(new MissingFieldError(), 6),
                ConversionResult::value(9),
                ConversionResult::error(new MissingFieldError(), 15));

        $result = $coercer->coerce(["a" => 2, "b" => 6, "c" => 9, "d" => 15], "map<int>", ["int"], $this->ctx);
        $this->assertSame(["a" => 2, "b" => 6, "c" => 9, "d" => 15], $result->getValue());
        $this->assertCount(2, $result->getErrors());
        $this->assertInstanceOf(MissingFieldError::class, $result->getErrors()[0]);
        $this->assertSame("b", $result->getErrors()[0]->getField());
        $this->assertInstanceOf(MissingFieldError::class, $result->getErrors()[1]);
        $this->assertSame("d", $result->getErrors()[1]->getField());
    }

    public function testTypedValueTerminalError()
    {
        $coercer = new MapCoercer();

        $this->ctx->expects($this->exactly(2))->method("coerce")
            ->withConsecutive(
                [2, "int"],
                [6, "int"])
            ->willReturnOnConsecutiveCalls(
                ConversionResult::value(2),
                ConversionResult::error(new TypeError("object", "int")));

        $result = $coercer->coerce(["a" => 2, "b" => 6, "c" => 9, "d" => 15], "map<int>", ["int"], $this->ctx);
        $this->assertNull($result->getValue());
        $this->assertCount(1, $result->getErrors());
        $this->assertInstanceOf(TypeError::class, $result->getErrors()[0]);
        $this->assertSame("b", $result->getErrors()[0]->getField());
    }

    public function testTypedKeyValueNonTerminalErrors()
    {
        $coercer = new MapCoercer();

        $this->ctx->expects($this->exactly(6))->method("coerce")
            ->withConsecutive(
                [2, "int"],
                ["a", "string"],
                [6, "int"],
                ["b", "string"],
                [9, "int"],
                ["c", "string"])
            ->willReturnOnConsecutiveCalls(
                ConversionResult::value(2),
                ConversionResult::value("a"),
                ConversionResult::error(new MissingFieldError(), 6),
                ConversionResult::value("b"),
                ConversionResult::value(9),
                ConversionResult::error(new MissingFieldError(), "c"));

        $result = $coercer->coerce(["a" => 2, "b" => 6, "c" => 9], "map<string, int>", ["string", "int"], $this->ctx);
        $this->assertSame(["a" => 2, "b" => 6, "c" => 9], $result->getValue());
        $this->assertCount(2, $result->getErrors());
        $this->assertInstanceOf(MissingFieldError::class, $result->getErrors()[0]);
        $this->assertSame("b", $result->getErrors()[0]->getField());
        $this->assertInstanceOf(MissingFieldError::class, $result->getErrors()[1]);
        $this->assertSame("c", $result->getErrors()[1]->getField());
    }

    public function testTypedKeyValueTerminalError()
    {
        $coercer = new MapCoercer();

        $this->ctx->expects($this->exactly(2))->method("coerce")
            ->withConsecutive(
                [2, "int"],
                ["a", "string"])
            ->willReturnOnConsecutiveCalls(
                ConversionResult::value(2),
                ConversionResult::error(new TypeError("object", "int")));

        $result = $coercer->coerce(["a" => 2, "b" => 6, "c" => 9, "d" => 15], "map<string, int>", ["string", "int"], $this->ctx);
        $this->assertNull($result->getValue());
        $this->assertCount(1, $result->getErrors());
        $this->assertInstanceOf(TypeError::class, $result->getErrors()[0]);
        $this->assertSame("a", $result->getErrors()[0]->getField());
    }
}
