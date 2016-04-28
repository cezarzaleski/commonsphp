<?php
namespace Commons\Pattern\Criteria\Impl;

use Commons\Pattern\Criteria\Criteria;

/**
 * Classe responsável por retornar a união exclusiva de dois conjuntos filtrados.
 * Ou seja elementos da interseção dos conjuntos filtrados serão descartados.
 *
 * Em teoria de conjuntos é o equivalente à diferença simétrica.
 */
class XorCriteria implements Criteria
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
        $leftFilter = $this->leftCriteria->meetCriteria($items);
        $rightFilter = $this->rightCriteria->meetCriteria($items);
        return array_diff(array_merge($leftFilter, $rightFilter), array_intersect($leftFilter, $rightFilter));
    }
}
