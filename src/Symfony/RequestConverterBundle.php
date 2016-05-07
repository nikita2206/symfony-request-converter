<?php

namespace RequestConverter\Symfony;

use RequestConverter\Symfony\DependencyInjection\RequestConverterExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RequestConverterBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    protected function createContainerExtension()
    {
        return new RequestConverterExtension();
    }
}
