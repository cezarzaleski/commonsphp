<?php

namespace Commons\Pattern\Criteria\Impl;

use Commons\Pattern\Criteria\Criteria;

/**
 * Classe responsável por retornar itens que não atendem ao filtro.
 * Retorna a diferença entre o conjunto de itens e o conjunto filtrado.
 *
 * Em teoria de conjuntos é o equivalente à diferença.
 */
class NotCriteria implements Criteria
{
    /**
     * @var Criteria
     */
    private $criteria;

    /**
     * Construtor padrão.
     *
     * @param Criteria $criteria
     */
    public function __construct(Criteria $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
    * {@inheritDoc}
    * @see \Commons\Pattern\Criteria\Criteria::meetCriteria()
    */
    public function meetCriteria(array $items)
    {
        return array_diff($items, $this->criteria->meetCriteria($items));
    }
}
