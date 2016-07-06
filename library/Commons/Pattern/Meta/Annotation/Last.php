<?php

namespace Commons\Pattern\Meta\Annotation;

/**
 * @Annotation
 * @Target({"METHOD", "PROPERTY", "CLASS"})
 */
class Last
{
    /**
     * @Required
     * @var string
     */
    public $value;
}
