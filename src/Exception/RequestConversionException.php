<?php

namespace RequestConverter\Exception;

use RequestConverter\Validation\Error;

class RequestConversionException extends \RuntimeException
    implements BadRequestException
{
    /**
     * @var Error[]
     */
    private $errors;

    public function __construct(array $errors)
    {
        parent::__construct("Bad request: the request was not up to specification");

        $this->errors = $errors;
    }

    /**
     * @return Error[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
