<?php

namespace CommonsTest\Country\Brazil\Validator;

use Commons\Util\Test\GenericTestCase;

/**
 * Classe CpfValidatorTest.
 */
class CpfValidatorTest extends GenericTestCase
{
    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        return 'Commons\Country\Brazil\Validator\CpfValidator';
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
                    array(true, array('652.066.042-67')),
                    array(true, array('65206604267')),
                    array(false, array('652.066.042-57')),
                    array(false, array('652.066.042-68')),
                    array(false, array('000.000.000-00')),
                    array(false, array('111.111.111-11')),
                    array(false, array('222.222.222-22')),
                    array(false, array('333.333.333-33')),
                    array(false, array('444.444.444-44')),
                    array(false, array('555.555.555-55')),
                    array(false, array('666.666.666-66')),
                    array(false, array('777.777.777-77')),
                    array(false, array('888.888.888-88')),
                    array(false, array('999.999.999-99')),
                    array(false, array(null))
                );
    }
}
