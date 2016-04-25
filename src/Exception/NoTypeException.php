<?php

namespace RequestConverter\Exception;

class NoTypeException extends \UnexpectedValueException
    implements CoercionException
{
    /**
     * @param string $coercerType
     * @param string $typeDefinition
     *
     * @return NoTypeException
     */
    public static function from($coercerType, $typeDefinition)
    {
        return new NoTypeException("Couldn't find coercer for type {$coercerType} from {$typeDefinition}");
    }
}
