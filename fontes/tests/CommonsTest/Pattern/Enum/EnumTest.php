<?php

namespace CommonsTest\Pattern\Enum;

use Commons\Util\Test\GenericTestCase;

/**
 * Classe EnumTest.
 */
class EnumTest extends GenericTestCase
{
    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createClassDefinition()
    {
        return new MockEnum();
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
        $classDef = $this->createClassDefinition();
        $this->assertPublicInterface($classDef, '__construct', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método __construct.
     */
    public function publicInterface__construct()
    {
        return array(
            array(
                new \Exception('Valor valorPadrao inválido para enumeração CommonsTest\Pattern\Enum\MockEnum.'),
                array('valorPadrao')),
            array(null, array('mock')),
            array(null, array(null))
        );
    }

    /**
     * Testa interface do método ValueOf.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceValueOf
     */
    public function testValueOf($expectedResult, $params)
    {
        $classDef = $this->createClassDefinition();
        $this->assertPublicInterface($classDef, 'valueOf', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método ValueOf.
     */
    public function publicInterfaceValueOf()
    {
        return array(
                    array(false, array('valorInexistente')),
                    array(false, array('DEFAULT_VALUE')),
                    array(false, array('mock')),
                    array('TESTE', array('teste')),
                    array(false, array(null))
                );
    }

    /**
     * Testa interface do método IsValid.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceIsValid
     */
    public function testIsValid($expectedResult, $params)
    {
        $classDef = $this->createClassDefinition();
        $this->assertPublicInterface($classDef, 'isValid', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método IsValid.
     */
    public function publicInterfaceIsValid()
    {
        return array(
                    array(false, array('DEFAULT_VALUE')),
                    array(true, array('teste')),
                    array(false, array(null))
                );
    }

    /**
     * Testa interface do método Values.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceValues
     */
    public function testValues($expectedResult, $params)
    {
        $classDef = $this->createClassDefinition();
        $this->assertPublicInterface($classDef, 'values', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método Values.
     */
    public function publicInterfaceValues()
    {
        return array( array( array('TESTE' => 'teste') , null ) );
    }

    /**
     * Testa interface do método __toString.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterface__toString
     */
    public function test__toString($expectedResult, $params)
    {
        $classDef = $this->createClassDefinition();
        $this->assertPublicInterface($classDef, '__toString', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método __toString.
     */
    public function publicInterface__toString()
    {
        return array( array( 'mock' , null ) );
    }
}
