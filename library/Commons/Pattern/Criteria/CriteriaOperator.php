<?php

namespace Commons\Pattern\Criteria;

use Commons\Pattern\Criteria\Impl\AndCriteria;
use Commons\Pattern\Criteria\Impl\DiffCriteria;
use Commons\Pattern\Criteria\Impl\NotCriteria;
use Commons\Pattern\Criteria\Impl\OrCriteria;
use Commons\Pattern\Criteria\Impl\XorCriteria;
use Commons\Pattern\Criteria\Impl\ClosureCriteria;
use Commons\Pattern\Criteria\Impl\AllCriteria;

/**
 * Define os operadores básicos para manipulação de criterions (filtros).
 */
class CriteriaOperator
{
    /**
     * Cria um Criterion (filtro) baseado em $closure.
     *
     * @param callback $closure
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public static function criterion($closure)
    {
        return new ClosureCriteria($closure);
    }

    /**
     * Cria um Criterion que retorna tudo (sem filtro).
     *
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public static function doAll()
    {
        return new AllCriteria();
    }

    /**
     * Cria uma operação AND.
     *
     * @param Criteria $left
     * @param Criteria $right
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public static function doAnd(Criteria $left, Criteria $right)
    {
        return new AndCriteria($left, $right);
    }

    /**
     * Cria uma operação DIFF.
     *
     * @param Criteria $left
     * @param Criteria $right
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public static function doDiff(Criteria $left, Criteria $right)
    {
        return new DiffCriteria($left, $right);
    }

    /**
     * Cria uma operação NOT.
     *
     * @param Criteria $criteria
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public static function doNot(Criteria $criteria)
    {
        return new NotCriteria($criteria);
    }

    /**
     * Cria uma operação OR.
     *
     * @param Criteria $left
     * @param Criteria $right
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public static function doOr(Criteria $left, Criteria $right)
    {
        return new OrCriteria($left, $right);
    }

    /**
     * Cria uma operação XOR.
     *
     * @param Criteria $left
     * @param Criteria $right
     * @return \Commons\Pattern\Criteria\Criteria
     */
    public static function doXor(Criteria $left, Criteria $right)
    {
        return new XorCriteria($left, $right);
    }
}
