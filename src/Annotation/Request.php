<?php

namespace RequestConverter\Annotation;

/**
 * You need to mark all root request classes with this annotation in
 * order for it to be recognized by RequestParamConverter
 *
 * @Annotation
 * @Target({"CLASS"})
 */
class Request
{
}
