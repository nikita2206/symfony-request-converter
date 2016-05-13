<?php

namespace RequestConverter\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidationException extends \RuntimeException
    implements BadRequestException
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $violations;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        parent::__construct("Bad request: the request was not up to specification");

        $this->violations = $violations;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
