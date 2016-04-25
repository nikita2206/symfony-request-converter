<?php

namespace RequestConverter\Coercion;

use RequestConverter\Context;
use RequestConverter\ConversionResult;
use RequestConverter\Validation\TypeError;
use RequestConverter\Validation\UncoercibleValueError;

class IntCoercer implements TypeCoercer
{
    /**
     * @inheritdoc
     */
    public function coerce($value, array $typeParams, Context $ctx)
    {
        if ( ! \is_int($value) && ! \is_string($value)) {
            return ConversionResult::error(new TypeError(\gettype($value), 'int'));
        }
        if (\is_string($value)) {
            $value = \filter_var($value, FILTER_VALIDATE_INT);

            if ($value === false) {
                return ConversionResult::error(new UncoercibleValueError('string', 'int'));
            }
        }

        return ConversionResult::value($value);
    }
}
