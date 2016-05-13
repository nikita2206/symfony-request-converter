<?php

namespace RequestConverter\Coercion;

use RequestConverter\Context;
use RequestConverter\ConversionResult;
use RequestConverter\Validation\TypeError;

class BoolCoercer implements TypeCoercer
{
    private $false = ["no", "false", "N", "F"];
    private $true = ["yes", "true", "Y", "T"];

    /**
     * @inheritdoc
     */
    public function coerce($value, array $typeParams, Context $ctx)
    {
        if ( ! \is_bool($value) && ! \is_int($value) && ! \is_string($value) && ! \is_float($value)) {
            return ConversionResult::error(new TypeError(\gettype($value), 'bool'));
        }

        if (\is_string($value)) {
            $value = trim($value);

            if (\is_numeric($value)) {
                $value = (bool)(int)$value;
            } elseif (\in_array($value, $this->false, true)) {
                $value = false;
            } elseif (\in_array($value, $this->true, true)) {
                $value = true;
            } else {
                $value = (bool)$value;
            }
        } else {
            $value = (bool)$value;
        }

        return ConversionResult::value($value);
    }
}
