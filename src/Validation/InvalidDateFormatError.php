<?php

namespace RequestConverter\Validation;

class InvalidDateFormatError extends Error
{
    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $format;

    /**
     * @param string $date
     * @param string $format
     */
    public function __construct($date, $format)
    {
        parent::__construct();

        $this->date = $date;
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }
}
