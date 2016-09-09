<?php

namespace RequestConverter\Coercion;

use RequestConverter\Context;
use RequestConverter\ConversionResult;
use RequestConverter\Exception\InvalidTypeException;
use RequestConverter\Validation\TypeError;

class MapCoercer implements TypeCoercer
{
    /**
     * @inheritdoc
     */
    public function coerce($value, $origType, array $typeParams, Context $ctx)
    {
        if ( ! \is_array($value)) {
            return ConversionResult::error(new TypeError(\gettype($value), 'object'));
        }

        $errors = [];
        if (\count($typeParams) === 1) { // coerced values
            $type = $typeParams[0];
            $coerced = [];
            foreach ($value as $k => $v) {
                $result = $ctx->coerce($v, $type);
                if ($result->getErrors()) {
                    $errors = array_merge($errors, $result->errorsInField($k));

                    if ($result->getValue() === null) {
                        return ConversionResult::errors($errors);
                    }
                }

                $coerced[$k] = $result->getValue();
            }

            $value = $coerced;
        } elseif (\count($typeParams) === 2) { // coerced keys and values
            list($keyT, $valueT) = $typeParams;

            if ($keyT !== "string" && $keyT !== "int") {
                throw InvalidTypeException::from("Map<{$keyT}, {$valueT}>");
            }

            $coerced = [];
            foreach ($value as $key => $val) {
                $v = $ctx->coerce($val, $valueT);
                $k = $ctx->coerce($key, $keyT);

                if ($v->getErrors() || $k->getErrors()) {
                    $errors = array_merge($errors, $v->errorsInField($key), $k->errorsInField($key));

                    if ($v->getValue() === null || $k->getValue() === null) {
                        return ConversionResult::errors($errors);
                    }
                }

                $coerced[$k->getValue()] = $v->getValue();
            }
            $value = $coerced;
        }

        return ConversionResult::errors($errors, $value);
    }
}
