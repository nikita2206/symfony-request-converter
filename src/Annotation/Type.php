<?php

namespace RequestConverter\Annotation;

/**
 * You can use this annotation on properties for type coercion
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
class Type
{
    /**
     * Field type to be used, can be one of:
     *  * Fully qualified class name, e.g.: Foo\Bar
     *  * Basic PHP type: bool, int, float, string, array
     *  * Typed arrays: array<FQCN> or array<bool>
     *  * Typed maps: Map<int, Any> or Map<string, Any> (Map will be coerced to PHP array which supports
     *      only string or integer keys)
     *
     * @var string
     */
    public $type;
}
