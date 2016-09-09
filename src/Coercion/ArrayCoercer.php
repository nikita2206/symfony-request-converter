<?php

namespace RequestConverter\Coercion;

use RequestConverter\Context;
use RequestConverter\ConversionResult;
use RequestConverter\Validation\TypeError;

class ArrayCoercer implements TypeCoercer
{
    /**
     * @inheritdoc
     */
    public function coerce($value, $origType, array $typeParams, Context $ctx)
    {
        if ( ! \is_array($value)) {
            return ConversionResult::error(new TypeError(\gettype($value), 'array'));
        }

        $errors = [];
        if ($typeParams) {
            $type = $typeParams[0];
            $coerced = [];
            $idx = 0;
            foreach ($value as $v) {
                $result = $ctx->coerce($v, $type);
                if ($result->getErrors()) {
                    $errors = \array_merge($errors, $result->errorsInIdx($idx));

                    if ($result->getValue() === null) {
                        return ConversionResult::errors($errors);
                    }
                }

                $coerced[] = $result->getValue();
                ++$idx;
            }

            $value = $coerced;
        } else {
            $value = \array_values($value);
        }

        return ConversionResult::errors($errors, $value);
    }
}
