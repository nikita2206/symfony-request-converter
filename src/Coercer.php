<?php

namespace RequestConverter;

use Doctrine\Common\Annotations\Annotation;
use RequestConverter\Coercion\TypeCoercer;
use RequestConverter\Exception\InvalidTypeException;
use RequestConverter\Exception\NoTypeException;

class Coercer
{
    /**
     * @var TypeCoercer[]
     */
    private $typeCoercers;

    public function __construct(array $typeCoercers)
    {
        $this->typeCoercers = $typeCoercers;
    }

    /**
     * @param mixed $value
     * @param string $type
     * @param Context $ctx
     *
     * @return ConversionResult
     * @throws NoTypeException
     * @throws InvalidTypeException
     */
    public function coerce($value, $type, Context $ctx)
    {
        if ($value === null) {
            return ConversionResult::value(null);
        }

        list($coerceType, $parameters) = $this->parseType($type);

        return $this->getCoercer($coerceType, $type)->coerce($value, $type, $parameters, $ctx);
    }

    /**
     * @param string $type
     *
     * @return array [$type, $parameters]
     * @throws InvalidTypeException
     */
    protected function parseType($type)
    {
        if ( ! \preg_match('!^(?:(?<basic_type>array|int|bool|float|string|Map|Date)|(?<class_type>[^<]+))(?:\<(?<t_params>.+?)\>)?$!', $type, $matches)) {
            throw InvalidTypeException::from($type);
        }

        $parameters = isset($matches["t_params"]) && \strlen($matches["t_params"]) ? \explode(",", $matches["t_params"]) : [];
        $parameters = \array_map("trim", $parameters);

        if (isset($matches["basic_type"]) && \strlen($matches["basic_type"])) {
            $coerceType = strtolower($matches["basic_type"]);
        } else {
            $coerceType = "object";
            \array_unshift($parameters, $matches["class_type"]);
        }

        return [$coerceType, $parameters];
    }

    /**
     * @param string $type
     * @param string $typeDefinition
     *
     * @return TypeCoercer
     * @throws NoTypeException
     */
    protected function getCoercer($type, $typeDefinition)
    {
        if ( ! isset($this->typeCoercers[$type])) {
            throw NoTypeException::from($type, $typeDefinition);
        }

        return $this->typeCoercers[$type];
    }
}
