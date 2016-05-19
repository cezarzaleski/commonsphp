<?php

namespace Commons\Pattern\Meta\Annotation;

/**
 * @Annotation
 * @Target({"METHOD", "PROPERTY", "CLASS"})
 */
class After
{
    /**
     * @Required
     * @var string
     */
    public $value;
}
