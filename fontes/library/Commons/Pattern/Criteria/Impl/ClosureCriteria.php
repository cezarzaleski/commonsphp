<?php

namespace Commons\Pattern\Criteria\Impl;

use Commons\Pattern\Criteria\Criteria;

/**
 * Cria uma Criteria baseada em uma closure.
 *
 * A função deve ter pelo menos um parâmetro que receba arrays.
 */
class ClosureCriteria implements Criteria
{
    /**
     * Função que realizará o filtro.
     *
     * @var callback
     */
    private $closure;

    /**
     * Construtor padrão.
     * @param callback $closure
     */
    public function __construct($closure)
    {
        $this->closure = $closure;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Criteria\Criteria::meetCriteria()
     */
    public function meetCriteria(array $items)
    {
        $call = $this->closure;
        return $call($items);
    }
}
