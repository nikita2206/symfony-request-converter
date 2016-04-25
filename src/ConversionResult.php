<?php

namespace RequestConverter;

use RequestConverter\Validation\Error;

class ConversionResult
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var Error[]
     */
    private $errors;

    private function __construct($value, $errors)
    {
        $this->value = $value;
        $this->errors = $errors;
    }

    /**
     * @param mixed $value
     *
     * @return ConversionResult
     */
    public static function value($value)
    {
        return new ConversionResult($value, []);
    }

    /**
     * @param Error[] $errors
     * @param mixed $value
     *
     * @return ConversionResult
     */
    public static function errors($errors, $value = null)
    {
        return new ConversionResult($value, $errors);
    }

    /**
     * @param Error $error
     * @param null $value
     *
     * @return ConversionResult
     */
    public static function error($error, $value = null)
    {
        return new ConversionResult($value, [$error]);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Error[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $field
     *
     * @return Error[]
     */
    public function errorsInField($field)
    {
        return \array_map(function (Error $err) use ($field) {
            return $err->inField($field);
        }, $this->errors);
    }

    /**
     * @param string|int $idx
     *
     * @return Error[]
     */
    public function errorsInIdx($idx)
    {
        return \array_map(function (Error $err) use ($idx) {
            return $err->inIndex($idx);
        }, $this->errors);
    }
}
