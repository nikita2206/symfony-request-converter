<?php

namespace RequestConverter\Exception;

class InvalidTypeException extends \RuntimeException
    implements CoercionException
{
    /**
     * @param string $typeDefinition
     *
     * @return InvalidTypeException
     */
    public static function from($typeDefinition)
    {
        return new InvalidTypeException("Couldn't parse type {$typeDefinition}");
    }
}
