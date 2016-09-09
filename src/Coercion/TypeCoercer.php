<?php

namespace RequestConverter\Coercion;

use RequestConverter\Context;
use RequestConverter\ConversionResult;

interface TypeCoercer
{
    /**
     * @param mixed $value
     * @param string $origType
     * @param array $typeParams
     * @param Context $ctx
     * @return ConversionResult
     */
    public function coerce($value, $origType, array $typeParams, Context $ctx);
}
