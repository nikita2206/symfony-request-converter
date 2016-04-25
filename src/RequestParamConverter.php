<?php

namespace RequestConverter;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Inflector\Inflector;
use RequestConverter\Annotation\Optional;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use RequestConverter\Annotation\Request as RequestAnnotation;

class RequestParamConverter implements ParamConverterInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = new \ReflectionClass($configuration->getClass());
        $source = isset($configuration->getOptions()["query"]) ? $request->query : $request->request;
        $target = $class->newInstanceWithoutConstructor();
        $setter = new Setter($target);

        if ($configuration->getOptions()["query"]) {
            $nameMangling = [Inflector::class, "camelize"];
        } else {
            $nameMangling = function ($a) { return $a; };
        }

        $missing = [];
        foreach ($class->getProperties() as $prop) {
            $name = $prop->getName();
            $mangled = $nameMangling($name);
            $exists = $source->has($mangled);

            if ( ! $exists && ! $this->reader->getPropertyAnnotation($prop, Optional::class)) {
                $missing[] = $mangled;
                continue;
            }

            if ($exists) {
                $setter->set($name, $source->get($mangled));
            }
        }

        if ($missing) {
            throw new MissingFieldsException("validation exception: some fields were missing", $missing);
        }

        $request->attributes->set($configuration->getName(), $target);
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        if ($configuration->getClass() === null) {
            return false;
        }

        try {
            $class = new \ReflectionClass($configuration->getClass());
        } catch (\ReflectionException $e) {
            return false;
        }

        return $this->reader->getClassAnnotation($class, RequestAnnotation::class) !== null;
    }
}
