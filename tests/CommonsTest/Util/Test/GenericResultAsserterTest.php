<?php

namespace CommonsTest\Util\Test;

use Commons\Util\Test\GenericTestCase;
use Commons\Util\Test\ResultAsserterFactory;
use Commons\Util\Test\ResultAsserter;
use Commons\Exception\InvalidArgumentException;

/**
 * Classe GenericResultAsserterTest.
 */
class GenericResultAsserterTest extends GenericTestCase
{
    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        return ResultAsserterFactory::create(function ($test, $obj, $result){
            if ($test && $obj && $result) {
                $test->assertTrue(true);
                $test->assertEquals($test, $obj);
                $test->assertEquals($obj, $result);
            } else {
                throw new \Exception('Sem algum dos parâmetros');
            }
        });
    }

    /**
     * Testa interface do método __construct.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterface__construct
     */
    public function test__construct($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, '__construct', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método __construct.
     */
    public function publicInterface__construct()
    {
        return array(
                    array(ResultAsserterFactory::create(function ($test, $obj, $result){
                        $obj->assertResult($test, $obj, $result);
                    }), array(function($test, $obj, $result){
                        $test->assertTrue(true);
                    })),
                    array(ResultAsserterFactory::create(function ($test, $obj, $result){
                        try {
                            $obj->assertResult($test, $obj, $result);
                            $test->fail('Não foi inserido o callback do ResultAsserter.');
                        } catch(\Exception $e) {
                            $test->assertEquals(new InvalidArgumentException('Undefined callback for ResultAsserter.'),$e);
                        }
                    }), array(null))
                );
    }

    /**
     * Testa interface do método AssertResult.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceAssertResult
     */
    public function testAssertResult($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'assertResult', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método AssertResult.
     */
    public function publicInterfaceAssertResult()
    {
        $exception = new \Exception('Sem algum dos parâmetros');
        return array(
                    array(null, array($this, $this, $this)),
                    array($exception, array($this, $this, null)),
                    array($exception, array($this, null, $this)),
                    array($exception, array($this, null, null)),
                    array($exception, array(null, $this, $this)),
                    array($exception, array(null, $this, null)),
                    array($exception, array(null, null, $this)),
                    array($exception, array(null, null, null))
                );
    }

}
