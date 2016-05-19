<?php

namespace Commons\Pattern\Transaction\Annotation;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class Transactional
{
    /** @Required */
    public $unit;
}
