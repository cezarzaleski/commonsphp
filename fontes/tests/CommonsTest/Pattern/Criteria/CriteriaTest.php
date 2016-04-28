<?php

namespace CommonsTest\Pattern\Criteria;

use Commons\Pattern\Criteria\CriteriaOperator;
use Commons\Pattern\Criteria\CriteriaBuilder;

class CriteriaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $dados
     * @dataProvider dados
     */
    public function testCriterion($dados) {
        $criterion = $this->getCriterionTwoFirstItems();
        $result = $criterion->meetCriteria($dados);
        self::assertEquals(array("a"=>"primeiro", "b"=>"segundo"),$result);
    }

    /**
     * @param array $dados
     * @dataProvider dados
     */
    public function testCriterionNot($dados) {
        $criterion = $this->getCriterionTwoFirstItems();
        $result = $criterion->not()->meetCriteria($dados);
        self::assertEquals(array("c"=>"terceiro"),$result);
    }

    /**
     * @param array $dados
     * @dataProvider dados
     */
    public function testCriterionDiff($dados) {
        $criterion1 = $this->getCriterionTwoFirstItems();
        $criterion2 = $this->getCriterionTwoLastItems();

        $result = $criterion1->diffWith($criterion2)->meetCriteria($dados);
        self::assertEquals(array("a"=>"primeiro"),$result);
    }

    /**
     * @param array $dados
     * @dataProvider dados
     */
    public function testCriterionAnd($dados) {
        $criterion1 = $this->getCriterionTwoFirstItems();
        $criterion2 = $this->getCriterionTwoLastItems();

        $result = $criterion1->andWith($criterion2)->meetCriteria($dados);
        self::assertEquals(array("b"=>"segundo"),$result);
    }

    /**
     * @param array $dados
     * @dataProvider dados
     */
    public function testCriterionOr($dados) {
        $criterion1 = $this->getCriterionTwoFirstItems();
        $criterion2 = $this->getCriterionTwoLastItems();

        $result = $criterion1->orWith($criterion2)->meetCriteria($dados);
        self::assertEquals(array("a"=>"primeiro", "b"=>"segundo", "c"=>"terceiro"),$result);
    }

    /**
     * @param array $dados
     * @dataProvider dados
     */
    public function testCriterionXor($dados) {
        $criterion1 = $this->getCriterionTwoFirstItems();
        $criterion2 = $this->getCriterionTwoLastItems();

        $result = $criterion1->xorWith($criterion2)->meetCriteria($dados);
        self::assertEquals(array("a"=>"primeiro", "c"=>"terceiro"),$result);
    }

    /**
     * @param array $dados
     * @dataProvider dados
     */
    public function testCriterionAll($dados) {
        $builder = new CriteriaBuilder(CriteriaOperator::doAll());

        $result = $builder->meetCriteria($dados);
        self::assertEquals(array("a"=>"primeiro", "b"=>"segundo", "c"=>"terceiro"),$result);

        $result = $builder->not()->meetCriteria($dados);
        self::assertEmpty($result);
    }

    public function getCriterionTwoFirstItems() {
        return new CriteriaBuilder(
            CriteriaOperator::criterion(
                function($items){
                    return array_slice($items, 0, 2);
                }
                )
            );
    }

    public function getCriterionTwoLastItems() {
        return new CriteriaBuilder(
            CriteriaOperator::criterion(
                function($items){
                    return array_slice($items, -2, 2);
                }
                )
            );
    }

    public function dados(){
        return array(
            array(
                array("a"=>"primeiro", "b"=>"segundo","c"=>"terceiro")
            )
        );
    }
}
