<?php

namespace RequestConverter;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Inflector\Inflector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use RequestConverter\Annotation\Request as RequestAnnotation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestParamConverter implements ParamConverterInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(Reader $reader, Converter $converter, ValidatorInterface $validator)
    {
        $this->reader = $reader;
        $this->converter = $converter;
        $this->validator = $validator;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $isQuery = isset($configuration->getOptions()["query"]);

        $source = $isQuery ? $request->query->all() : $request->request->all();
        $nameMangling = $isQuery ? [Inflector::class, "tableize"] : function ($a) { return $a; };

        $class = new \ReflectionClass($configuration->getClass());

        $result = $this->converter->convert($source, $class, $nameMangling);

        if ( ! $result->getErrors()) {
            $violations = $this->validator->validate($result->getValue());

            if ( ! $violations->count()) {
                $request->attributes->set($configuration->getName(), $result->getValue());
            }
        }
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
