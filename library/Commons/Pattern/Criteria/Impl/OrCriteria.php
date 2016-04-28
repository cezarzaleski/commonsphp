<?php
namespace Commons\Pattern\Criteria\Impl;

use Commons\Pattern\Criteria\Criteria;

/**
 * Classe responsável por retornar a união entre dois conjuntos filtrados.
 *
 * Em teoria de conjuntos é o equivalente à união.
 */
class OrCriteria implements Criteria
{
    /**
     * Operando esquerdo.
     * @var Criteria
     */
    private $leftCriteria;

    /**
     * Operando direito.
     * @var Criteria
     */
    private $rightCriteria;

    /**
     * Construtor padrão.
     *
     * @param Criteria $criteriaA
     * @param Criteria $criteriaB
     */
    public function __construct(Criteria $criteriaA, Criteria $criteriaB)
    {
        $this->leftCriteria = $criteriaA;
        $this->rightCriteria = $criteriaB;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Criteria\Criteria::meetCriteria()
     */
    public function meetCriteria(array $items)
    {
        return array_merge($this->leftCriteria->meetCriteria($items), $this->rightCriteria->meetCriteria($items));
    }
}
