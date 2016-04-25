<?php

namespace RequestConverter\Coercion;

use RequestConverter\Context;
use RequestConverter\ConversionResult;

interface TypeCoercer
{
    /**
     * @param mixed $value
     * @param array $typeParams
     * @param Context $ctx
     *
     * @return ConversionResult
     */
    public function coerce($value, array $typeParams, Context $ctx);
}
