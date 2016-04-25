<?php

namespace RequestConverter\Exception;

class ConverterException extends \RuntimeException
{
    /**
     * @param string $className
     * @param string $property
     * @param \Exception $previous
     *
     * @return ConverterException
     */
    public static function in($className, $property, \Exception $previous)
    {
        return new ConverterException("Couldn't convert in {$className}::{$property}. {$previous->getMessage()}", 0, $previous);
    }
}
