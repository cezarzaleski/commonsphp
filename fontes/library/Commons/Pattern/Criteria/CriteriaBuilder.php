<?php

namespace Commons\Pattern\Criteria;

/**
 * Responsável por construir Criteria compostas.
 */
class CriteriaBuilder implements Criteria
{
    /**
     * @var \Commons\Pattern\Criteria\Criteria
     */
    private $criteria;

    /**
     * Construtor padrão.
     *
     * @param Criteria $startCriteria
     */
    public function __construct(Criteria $startCriteria)
    {
        $this->criteria = $startCriteria;
    }

    /**
     * Cria uma operação AND.
     *
     * @param Criteria $criteria
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public function andWith(Criteria $criteria)
    {
         $this->criteria = CriteriaOperator::doAnd($this->criteria, $criteria);
         return $this;
    }

    /**
     * Cria uma operação DIFF.
     *
     * @param Criteria $criteria
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public function diffWith(Criteria $criteria)
    {
        $this->criteria = CriteriaOperator::doDiff($this->criteria, $criteria);
        return $this;
    }

    /**
     * Cria uma operação NOT.
     *
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public function not()
    {
        $this->criteria = CriteriaOperator::doNot($this->criteria);
        return $this;
    }

    /**
     * Cria uma operação OR.
     *
     * @param Criteria $criteria
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public function orWith(Criteria $criteria)
    {
        $this->criteria = CriteriaOperator::doOr($this->criteria, $criteria);
        return $this;
    }

    /**
     * Cria uma operação XOR.
     *
     * @param Criteria $criteria
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public function xorWith(Criteria $criteria)
    {
        $this->criteria = CriteriaOperator::doXor($this->criteria, $criteria);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Criteria\Criteria::meetCriteria()
     */
    public function meetCriteria(array $items)
    {
        return $this->criteria->meetCriteria($items);
    }
}
