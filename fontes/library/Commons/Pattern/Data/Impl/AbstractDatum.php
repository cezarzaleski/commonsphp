<?php

namespace Commons\Pattern\Data\Impl;

use Commons\Pattern\Data\Datum;
use Zend\Hydrator\ClassMethods;

/**
 * Implementação básica do Datum.
 */
abstract class AbstractDatum implements Datum
{
    /**
     * Construtor padrão.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        if (!empty($options)) {
            $this->fromArray($options);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Data\Datum::toArray()
     */
    public function toArray()
    {
        $hydrator = new ClassMethods(false);
        return $hydrator->extract($this);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Data\Datum::fromArray()
     */
    public function fromArray(array $options)
    {
        $hydrator = new ClassMethods();
        $hydrator->hydrate($options, $this);
    }
}
