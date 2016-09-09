<?php

namespace RequestConverter\Coercion;

use RequestConverter\Context;
use RequestConverter\ConversionResult;
use RequestConverter\Exception\InvalidTypeException;
use RequestConverter\Validation\InvalidDateFormatError;

class DateCoercer implements TypeCoercer
{
    /**
     * @var \DateTimeZone
     */
    private $timezone;

    public function __construct(\DateTimeZone $tz = null)
    {
        $this->timezone = $tz ?: new \DateTimeZone(date_default_timezone_get());
    }

    /**
     * @inheritdoc
     */
    public function coerce($value, $origType, array $typeParams, Context $ctx)
    {
        if ( ! $typeParams || ! \preg_match('!^[^<]+\<(.+)\>$!', $origType, $formatMatch)) {
            throw new InvalidTypeException("DateTime type expects format to be provided");
        }

        $format = $formatMatch[1];

        $date = \DateTime::createFromFormat($format, $value, $this->timezone);
        if ( ! $date) {
            return ConversionResult::error(new InvalidDateFormatError($value, $format));
        }

        return ConversionResult::value($date);
    }
}
