<?php

namespace RequestConverter;

use Doctrine\Common\Annotations\Reader;
use RequestConverter\Annotation\Optional;
use RequestConverter\Annotation\Type;
use RequestConverter\Exception\CoercionException;
use RequestConverter\Exception\ConverterException;
use RequestConverter\Validation\MissingFieldError;

class Converter
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Coercer
     */
    private $coercer;

    public function __construct(Reader $annotationReader, Coercer $coercer)
    {
        $this->reader = $annotationReader;
        $this->coercer = $coercer;
    }

    /**
     * @param array $request
     * @param \ReflectionClass $class
     * @param callable $nameMangling
     *
     * @return ConversionResult
     */
    public function convert(array $request, \ReflectionClass $class, callable $nameMangling)
    {
        $ctx = new Context($this, $this->coercer, $nameMangling);
        $object = $this->instance($class);
        $setter = $this->setter($object);

        $errors = [];
        foreach ($class->getProperties() as $prop) {
            $name = $nameMangling($prop->getName());

            if ( ! isset($request[$name]) && ! $this->reader->getPropertyAnnotation($prop, Optional::class)) {
                $errors[] = new MissingFieldError($name);
                continue;
            } elseif ( ! \array_key_exists($name, $request)) {
                continue;
            }

            $value = $request[$name];
            $typeAnnotation = $this->reader->getPropertyAnnotation($prop, Type::class);

            if ($value !== null && $typeAnnotation instanceof Type) {
                try {
                    $result = $this->coercer->coerce($value, $typeAnnotation->type, $ctx);
                } catch (CoercionException $e) {
                    throw ConverterException::in($class->getName(), $prop->getName(), $e);
                }

                $value = $result->getValue();
                $errors = \array_merge($errors, $result->errorsInField($name));
            }

            $setter->set($prop->getName(), $value);
        }

        return ConversionResult::errors($errors, $object);
    }

    /**
     * @param object $object
     * @return Setter
     */
    protected function setter($object)
    {
        return new Setter($object);
    }

    /**
     * @param \ReflectionClass $class
     * @return object
     */
    protected function instance(\ReflectionClass $class)
    {
        return $class->newInstanceWithoutConstructor();
    }
}
