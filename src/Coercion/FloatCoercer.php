<?php

namespace RequestConverter\Coercion;

use RequestConverter\Context;
use RequestConverter\ConversionResult;
use RequestConverter\Validation\TypeError;
use RequestConverter\Validation\UncoercibleValueError;

class FloatCoercer implements TypeCoercer
{
    /**
     * @inheritdoc
     */
    public function coerce($value, array $typeParams, Context $ctx)
    {
        if ( ! \is_float($value) && ! \is_int($value) && ! \is_string($value)) {
            return ConversionResult::error(new TypeError(\gettype($value), 'float'));
        }
        if (\is_string($value)) {
            $value = \filter_var($value, FILTER_VALIDATE_FLOAT);

            if ($value === false) {
                return ConversionResult::error(new UncoercibleValueError('string', 'float'));
            }
        }

        return ConversionResult::value((float)$value);
    }
}
