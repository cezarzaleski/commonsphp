<?php

namespace CommonsTest\Country\Brazil\Validator;

use Commons\Util\Test\GenericTestCase;
use Commons\Country\Brazil\Validator\TelefoneValidator;

/**
 * Classe TelefoneValidatorTest.
 */
class TelefoneValidatorTest extends GenericTestCase
{
    /**
     * Método responsável por criar a definição da classe (telefone celular).
     *
     * @return mixed class name or object instance.
     */
    public function createDefinitionCelular()
    {
        return new TelefoneValidator(array('telefone.is_mobile' => true));
    }

    /**
     * Testa interface do método IsValid (celular).
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceIsValidCelular
     */
    public function testIsValidCelular($expectedResult, $params)
    {
        $classDef = $this->createDefinitionCelular();
        $this->assertPublicInterface($classDef, 'isValid', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método IsValid  (celular).
     */
    public function publicInterfaceIsValidCelular()
    {
        return array(
                    // números celular
                    array(true, array('556162222222')),
                    array(true, array('556172222222')),
                    array(true, array('556182222222')),
                    array(true, array('556192222222')),
                    // números celular formatação (codigo país)
                    array(true, array('+556162222222')),
                    array(true, array('+ 556162222222')),
                    array(true, array('+ (55)6162222222')),
                    array(true, array('+ (55) 6162222222')),
                    array(true, array('+(55) 6162222222')),
                    array(true, array('+(55)6162222222')),
                    array(true, array(' (55)6162222222')),
                    array(true, array(' (55) 6162222222')),
                    array(true, array(' (55)6162222222')),
                    array(true, array('(55) 6162222222')),
                    array(true, array('(55)6162222222')),
                    array(true, array(' 556162222222')),
                    array(true, array(' 556162222222')),
                    array(true, array(' 55 6162222222')),
                    array(true, array('55 6162222222')),
                    array(true, array('556162222222')),
                    // ddd reservado
                    array(false, array('551062222222')),
                    // opcionalidade do dígito 9 regioes antes de 2015.
                    array(true, array('556162222222')),
                    array(true, array('5561962222222')),
                    // na opcionalidade do dígito 9 para regiões antes de 2015 não permitir inclusão de novos números.
                    array(false, array('5561912222222')),
                    // obrigatoriedade do dígito 9 regioes SP, ES, RJ, MA, PA, AP, RR, AM (amostra 11).
                    array(false, array('551162222222')),
                    array(true, array('5511962222222')),
                    // novos números quando possuir o novo dígito 9 obrigatório (amostra 11).
                    array(true, array('5511912222222')),
                    // formato DDD
                    array(true, array('556162222222')),
                    array(true, array('55 6162222222')),
                    array(true, array('5561 62222222')),
                    array(true, array('55 61 62222222')),
                    array(true, array('55(61)62222222')),
                    array(true, array('55 (61)62222222')),
                    array(true, array('55(61) 62222222')),
                    array(true, array('55 (61) 62222222')),
                    // formato número
                    array(true, array('55616222-2222')),
                    // números fixos
                    array(false, array('556122222222')),
                    array(false, array('556133333333')),
                    array(false, array('556144444444')),
                    array(false, array('556155555555')),
                    // outras incorreções
                    array(false, array('6199999999')),
                    array(false, array('619999999999')),
                    array(false, array('556159999999')),
                    array(false, array('9')),
                    array(false, array('99999999')),
                    array(false, array('+55 (61) 2999-9999')),
                    array(false, array('+55 (612999-9999')),
                    array(false, array('+55 61)2999-9999')),
                    array(false, array('+(55 612999-9999')),
                    array(false, array('+55)612999-9999')),
                    array(false, array('abcdefghij')),
                    array(false, array('abcdefghijk')),
                    array(false, array(null))
                );
    }

    /**
     * Método responsável por criar a definição da classe padrão (telefone fixo).
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        return 'Commons\Country\Brazil\Validator\TelefoneValidator';
    }

    /**
     * Testa interface do método IsValid (telefone fixo).
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
     * Provedor de dados com a combinatória de testes para o método IsValid (telefone fixo).
     */
    public function publicInterfaceIsValid()
    {
        return array(
            // números fixos
            array(true, array('556122222222')),
            array(true, array('556133333333')),
            array(true, array('556144444444')),
            array(true, array('556155555555')),
            // números celulares
            array(false, array('556162222222')),
            array(false, array('556173333333')),
            array(false, array('556184444444')),
            array(false, array('556195555555')),
            // outras incorreções
            array(false, array('6155555555')),
            array(false, array('61555555555')),
            array(false, array('556199999999')),
            array(false, array('9')),
            array(false, array('99999999')),
            array(false, array('+55 (61) 9999-9999')),
            array(false, array('abcdefghij')),
            array(false, array('abcdefghijk')),
            array(false, array(null))
        );
    }

}
