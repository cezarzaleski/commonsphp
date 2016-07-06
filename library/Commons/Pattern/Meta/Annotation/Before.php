<?php

namespace Commons\Pattern\Meta\Annotation;

/**
 * @Annotation
 * @Target({"METHOD", "PROPERTY", "CLASS"})
 */
class Before
{
    /**
     * @Required
     * @var string
     */
    public $value;
}
