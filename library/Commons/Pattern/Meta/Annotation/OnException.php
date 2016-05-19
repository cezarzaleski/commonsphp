<?php

namespace Commons\Pattern\Meta\Annotation;

/**
 * @Annotation
 * @Target({"METHOD", "PROPERTY","CLASS"})
 */
class OnException
{
    /**
     * @Required
     * @var string
     */
    public $value;
}
