<?php

namespace TodoExample;

use RequestConverter\Exception\BadRequestException;
use RequestConverter\Exception\RequestConversionException;
use RequestConverter\Exception\RequestValidationException;
use RequestConverter\Validation\Error;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;

class InvalidRequestRenderer
{
    public function render(Request $request, BadRequestException $ex)
    {
        // This is a type error
        if ($ex instanceof RequestConversionException) {
            $type = "conversion";
            $errors = array_map(function (Error $err) {
                return ["field" => $err->getField(), "kind" => get_class($err)];
            }, $ex->getErrors());

        } elseif ($ex instanceof RequestValidationException) {
            $type = "validation";
            $errors = array_map(function (ConstraintViolationInterface $v) {
                return ["field" => $v->getPropertyPath(), "kind" => $v->getMessageTemplate()];
            }, iterator_to_array($ex->getViolations()));

        } else {
            throw new \RuntimeException("Couldn't render BadRequestException", 0, $ex);
        }

        return new Response(json_encode(["error" => $type, "errors" => $errors]), 400, ["Content-Type" => "application/json"]);
    }
}
