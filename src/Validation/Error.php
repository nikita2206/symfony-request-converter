<?php

namespace RequestConverter\Validation;

abstract class Error
{
    /**
     * @var string
     */
    private $field;

    /**
     * @param string $field
     */
    public function __construct($field = null)
    {
        $this->field = $field;
    }

    /**
     * @param string $field
     * @return Error
     */
    public function inField($field)
    {
        $new = clone $this;
        $new->field = $this->joinFields($field, $this->field);

        return $new;
    }

    public function inIndex($idx)
    {
        $new = clone $this;
        $new->field = $this->joinFields("[{$idx}]", $this->field);

        return $new;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $l
     * @param string|null $r
     *
     * @return string
     */
    private function joinFields($l, $r = null)
    {
        if ($r === null) {
            return $l;
        }

        if ($r[0] === "[") {
            return $l . $r;
        }

        return "{$l}.{$r}";
    }
}
