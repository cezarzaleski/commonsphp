<?php

namespace Commons\Pattern\Criteria\Impl;

use Commons\Pattern\Criteria\Criteria;

/**
 * Classe responsável apenas por retornar todos resultados sem nenhuma filtragem.
 */
class AllCriteria implements Criteria
{
    /**
    * {@inheritDoc}
    * @see \Commons\Pattern\Criteria\Criteria::meetCriteria()
    */
    public function meetCriteria(array $items)
    {
        return $items;
    }
}
