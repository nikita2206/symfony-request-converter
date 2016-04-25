<?php

namespace RequestConverter;

class Context
{
    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var Coercer
     */
    private $coercer;

    /**
     * @var callable(string): string
     */
    private $nameMangling;

    public function __construct(Converter $converter, Coercer $coercer, callable $nameMangling)
    {
        $this->converter = $converter;
        $this->coercer = $coercer;
        $this->nameMangling = $nameMangling;
    }

    /**
     * @param array $data
     * @param \ReflectionClass $class
     *
     * @return ConversionResult
     */
    public function convert(array $data, \ReflectionClass $class)
    {
        return $this->converter->convert($data, $class, $this->nameMangling);
    }

    /**
     * @param mixed $value
     * @param string $type
     *
     * @return ConversionResult
     */
    public function coerce($value, $type)
    {
        return $this->coercer->coerce($value, $type, $this);
    }
}
