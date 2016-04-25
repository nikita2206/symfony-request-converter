<?php

namespace RequestConverter;

class Setter
{
    /**
     * @var object
     */
    private $instance;

    /**
     * @var \Closure
     */
    private $set;

    /**
     * @param object $instance
     */
    public function __construct($instance)
    {
        $this->instance = $instance;

        $set = function ($name, $value) {
            $this->$name = $value;
        };
        $this->set = $set->bindTo($instance, \get_class($instance));
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        \call_user_func_array($this->set, [$name, $value]);
    }
}
