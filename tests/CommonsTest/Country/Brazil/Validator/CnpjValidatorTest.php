<?php

namespace CommonsTest\Country\Brazil\Validator;

use Commons\Util\Test\GenericTestCase;

/**
 * Classe CnpjValidatorTest.
 */
class CnpjValidatorTest extends GenericTestCase
{
    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        return 'Commons\Country\Brazil\Validator\CnpjValidator';
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
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'isValid', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método IsValid.
     */
    public function publicInterfaceIsValid()
    {
        return array(
                    array(true, array('26.013.563/0001-08')),
                    array(true, array('26013563000108')),
                    array(false, array('26.013.563/0001-18')),
                    array(false, array('26.013.563/0001-09')),
                    array(false, array('26.013.5630001-09')),
                    array(false, array('ab.cde.efg/hijk-lm')),
                    array(false, array(null)),
                );
    }
}
