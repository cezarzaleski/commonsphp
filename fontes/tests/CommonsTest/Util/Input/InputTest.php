<?php

namespace CommonsTest\Util\Input;

use Commons\Util\Input\InputException, Commons\Util\Input\Input;
use Zend\Validator\Digits, Zend\Validator\Between;

class InputTest extends \PHPUnit_Framework_TestCase
{

    public function testExceptionContent_addValidator()
    {
        try {
            $input = new Input();
            $input->addValidator('number', 'string', new Digits())->process();
            $this->fail('Sucesso inesperado, entrava inválida para o Commons\Util\Input\Input deveria gerar erro');
        } catch (InputException $e) {
            self::assertEquals($input, $e->getInput());
        }
    }

    public function testInput_success()
    {
        try {
            $input = new Input();
            $instance = $input->addValidator('number', '1', new Digits())->process();
            self::assertNotEmpty($instance);
        } catch (InputException $e) {
            $this->fail('Não deve ocorrer erro');
        }
    }

    public function testInput_success_withFilter()
    {
        try {
            $input = new Input();
            $instance = $input->addValidator('number', 1, new Digits(), true, true, array(
                'alnum'
            ))->process();
            self::assertNotEmpty($instance);
        } catch (InputException $e) {
            $this->fail('Não deve ocorrer erro');
        }
    }

    public function testInput_success_withFilterArray()
    {
        try {
            $input = new Input();
            $instance = $input->addValidator('number', '1', new Digits(), true, true, array(
                array(
                    'alnum',
                    array()
                )
            ))->process();
            self::assertNotEmpty($instance);
        } catch (InputException $e) {
            $this->fail('Não deve ocorrer erro');
        }
    }

    public function testProcessSuccess_addValidators()
    {
        $input = new Input();
        $input->addValidator('month', 3,
            // validações
            array(
                new Digits(),
                new Between(1, 12)
            ),
            // presença
            false,
            // permite vazio
            false,
            // filtro
            array(
                'digits',
                function ($value) {
                    // Like ToInt
                    if (!is_scalar($value)) {
                        return $value;
                    }
                    $value = (string) $value;

                    return (int) $value;
                }
            ))->addValidator('number', '13', new Digits());
        self::assertTrue($input->isValid());
    }

    /**
     *
     * @param string $month
     *            @dataProvider validMonth
     */
    public function testProcessSuccess_addValidator($month)
    {
        $input = new Input();
        $input->addValidator('month', $month,
            // validações
            array(
                new Digits(),
                new Between(1, 12)
            ),
            // presença
            false,
            // permite vazio
            false,
            // filtro
            array(
                'digits',
                'int'
            ));
        self::assertTrue($input->isValid());
    }

    public function validMonth()
    {
        return array(
            array(
                1
            ),
            array(
                12
            ),
            array(
                3
            ),
            array(
                10
            ),
            array(
                6
            )
        );
    }
}
