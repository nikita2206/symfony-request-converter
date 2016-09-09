<?php

namespace TodoExample\Request;

use RequestConverter\Annotation\Optional;
use RequestConverter\Annotation\Request;
use RequestConverter\Annotation\Type;
use Symfony\Component\Validator\Constraints as Valid;

/**
 * @Request()
 */
class Task implements \JsonSerializable
{
    /**
     * @var string
     * @Type("string")
     * @Valid\Length(min=1, max=255)
     */
    private $description;

    /**
     * @var \DateTime
     * @Type("Date<d/m/Y H:i>")
     * @Optional()
     */
    private $due;

    /**
     * @var int
     * @Type("int")
     * @Valid\Range(min=1, max=10)
     * @Optional()
     */
    private $priority = 5;

    /**
     * @var string
     * @Type("string")
     * @Optional()
     */
    private $notes;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return \DateTime
     */
    public function getDue()
    {
        return $this->due;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @inheritdoc
     */
    function jsonSerialize()
    {
        return ["due" => $this->due ? $this->due->format("d/m/Y H:i") : null] + get_object_vars($this);
    }
}
