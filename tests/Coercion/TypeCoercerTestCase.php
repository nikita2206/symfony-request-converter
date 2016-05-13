<?php

namespace Tests\Coercion;

use RequestConverter\Context;
use RequestConverter\ConversionResult;
use RequestConverter\Validation\TypeError;
use RequestConverter\Validation\UncoercibleValueError;

abstract class TypeCoercerTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var Context|\PHPUnit_Framework_MockObject_MockObject */
    public $ctx;

    public function setUp()
    {
        $this->ctx = $this->getMock(Context::class, [], [], '', false);
    }

    public function assertTypeError(ConversionResult $result)
    {
        $this->assertNull($result->getValue());
        $this->assertCount(1, $result->getErrors());
        $this->assertInstanceOf(TypeError::class, $result->getErrors()[0]);
    }

    public function assertUncoercible(ConversionResult $result)
    {
        $this->assertNull($result->getValue());
        $this->assertCount(1, $result->getErrors());
        $this->assertInstanceOf(UncoercibleValueError::class, $result->getErrors()[0]);
    }

    public function assertConvertedValue($expected, ConversionResult $result)
    {
        $this->assertEmpty($result->getErrors());
        $this->assertSame($expected, $result->getValue());
    }
}
