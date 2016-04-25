<?php

namespace RequestConverter\Validation;

class TypeError extends Error
{
    /**
     * @var string
     */
    private $givenType;

    /**
     * @var string
     */
    private $expectedType;

    /**
     * @param string $givenType
     * @param string $expectedType
     * @param string $field
     */
    public function __construct($givenType, $expectedType, $field = null)
    {
        parent::__construct();

        $this->givenType = $givenType;
        $this->expectedType = $expectedType;
    }

    /**
     * @return string
     */
    public function getGivenType()
    {
        return $this->givenType;
    }

    /**
     * @return string
     */
    public function getExpectedType()
    {
        return $this->expectedType;
    }
}
