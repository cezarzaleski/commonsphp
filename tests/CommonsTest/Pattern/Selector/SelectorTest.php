<?php

namespace CommonsTest\Pattern\Selector;

use Commons\Pattern\Selector\Selector;
use Commons\Pattern\Criteria\CriteriaOperator;
use Commons\Exception\InvalidArgumentException;

class SelectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dados
     * @param array $dados
     */
    public function testSelectorAll($dados) {
        $selector = new Selector($dados);
        $result = $selector->select(CriteriaOperator::doAll());

        self::assertEquals($dados, $result);
    }

    /**
     * @dataProvider dados
     * @param array $dados
     */
    public function testSelectorSatisfiedCondition($dados) {
        $selector = new Selector($dados);
        $result = $selector->select(
            CriteriaOperator::criterion(
                    function($dados){
                        return $dados["select1"];
                    }
                )
            );

        self::assertEquals("Hello world!", $result);
    }

    /**
     * @dataProvider dados
     * @param array $dados
     */
    public function testSelectorUnSatisfiedCondition($dados) {
        $selector = new Selector($dados, "select2");
        $result = $selector->select(
            CriteriaOperator::criterion(
                function($dados){
                    return isset($dados["select4"])? array("select4" => $dados["select4"]):null;
                }
                )
            );

        self::assertEquals("Hello test scope!", $result);
    }

    /**
     * @dataProvider dados
     * @param array $dados
     */
    public function testSelectorUnSatisfiedConditionDefaultUndefined($dados) {
        try {
            $selector = new Selector($dados);
            $selector->select(
                CriteriaOperator::criterion(
                        function($dados){
                            return null;
                        }
                    )
                );
        } catch (InvalidArgumentException $e) {
            self::assertEquals('Not found.', $e->getMessage());
            self::assertEquals(Selector::NOT_FOUND, $e->getCode());
        }
    }

    public function dados(){
        return array(
            array(
                array("select1"=>"Hello world!", "select2"=>"Hello test scope!","select3"=>"Hello folks!!")
            )
        );
    }
}
