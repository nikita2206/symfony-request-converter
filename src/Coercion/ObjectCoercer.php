<?php

namespace RequestConverter\Coercion;

use RequestConverter\Context;
use RequestConverter\ConversionResult;
use RequestConverter\Validation\TypeError;

class ObjectCoercer implements TypeCoercer
{
    /**
     * @inheritdoc
     */
    public function coerce($value, $origType, array $typeParams, Context $ctx)
    {
        if ( ! \is_array($value)) {
            return ConversionResult::error(new TypeError(\gettype($value), 'object'));
        }

        $class = new \ReflectionClass($typeParams[0]);

        return $ctx->convert($value, $class);
    }
}
