<?php

namespace RequestConverter\Coercion;

use RequestConverter\Context;
use RequestConverter\ConversionResult;
use RequestConverter\Validation\TypeError;

class StringCoercer implements TypeCoercer
{
    /**
     * @inheritdoc
     */
    public function coerce($value, $origType, array $typeParams, Context $ctx)
    {
        if ( ! \is_string($value) && ! \is_int($value) && ! \is_float($value)) {
            return ConversionResult::error(new TypeError(\gettype($value), 'string'));
        }

        return ConversionResult::value((string)$value);
    }
}
