<?php

namespace CommonsTest\Util\Input;

use Commons\Util\Test\GenericTestCase;
use Commons\Util\Test\ResultAsserterFactory;
use Commons\Util\Input\Input;
use Commons\Util\Input\InputException;

/**
 * Classe InputExceptionTest.
 */
class InputExceptionTest extends GenericTestCase
{
    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        return new InputException('teste', 1, new \Exception('causa'), new Input());
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
        $classDef = 'Commons\Util\Input\InputException';
        $this->assertPublicInterface($classDef, '__construct', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método __construct.
     */
    public function publicInterface__construct()
    {
        $input = new Input();
        return array(
                array(ResultAsserterFactory::create(function ($testeCase, $obj, $result) use ($input) {
                    $testeCase->assertEquals(new InputException('teste', 1, new \Exception('causa'), $input), $obj);
                }), array('teste', 1, new \Exception('causa'), $input)),
                array(ResultAsserterFactory::create(function ($testeCase, $obj, $result) use ($input) {
                    $testeCase->assertEquals(new InputException('teste', 1, null, $input), $obj);
                }), array('teste', 1, null, $input)),
                array(ResultAsserterFactory::create(function ($testeCase, $obj, $result) use ($input) {
                    $testeCase->assertEquals(new InputException('teste', null, new \Exception('causa'), $input), $obj);
                }), array('teste', null, new \Exception('causa'), $input)),
                array(ResultAsserterFactory::create(function ($testeCase, $obj, $result) use ($input) {
                    $testeCase->assertEquals(new InputException('teste', null, null, $input), $obj);
                }), array('teste', null, null, $input)),
                array(ResultAsserterFactory::create(function ($testeCase, $obj, $result) use ($input) {
                    $testeCase->assertEquals(new InputException(null, 1, new \Exception('causa'), $input), $obj);
                }), array(null, 1, new \Exception('causa'),$input)),
                array(ResultAsserterFactory::create(function ($testeCase, $obj, $result) use ($input) {
                    $testeCase->assertEquals(new InputException(null, 1, null, $input), $obj);
                }), array(null, 1, null, $input)),
                array(ResultAsserterFactory::create(function ($testeCase, $obj, $result) use ($input) {
                    $testeCase->assertEquals(new InputException(null, null, new \Exception('causa'), $input), $obj);
                }), array(null, null, new \Exception('causa'), $input)),
                array(ResultAsserterFactory::create(function ($testeCase, $obj, $result) use ($input) {
                    $testeCase->assertEquals(new InputException(null, null, null, $input), $obj);
                }), array(null, null, null, $input))
        );
    }

    /**
     * Testa interface do método GetInput.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetInput
     */
    public function testGetInput($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getInput', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetInput.
     */
    public function publicInterfaceGetInput()
    {
        return array( array( new Input() , null ) );
    }

    /**
     * Testa interface do método GetMessage.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetMessage
     */
    public function testGetMessage($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getMessage', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetMessage.
     */
    public function publicInterfaceGetMessage()
    {
        return array( array( 'teste' , null ) );
    }

    /**
     * Testa interface do método GetCode.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetCode
     */
    public function testGetCode($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getCode', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetCode.
     */
    public function publicInterfaceGetCode()
    {
        return array( array( 1 , null ) );
    }

    /**
     * Testa interface do método GetFile.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetFile
     */
    public function testGetFile($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getFile', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetFile.
     */
    public function publicInterfaceGetFile()
    {
        return array( array( ResultAsserterFactory::create(function ($test, $obj, $result) {
            $test->assertTrue(\preg_match('/^.*InputExceptionTest\.php$/', $result)===1);
        }) , null ) );
    }

    /**
     * Testa interface do método GetLine.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetLine
     */
    public function testGetLine($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getLine', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetLine.
     */
    public function publicInterfaceGetLine()
    {
        return array( array( 22 , null ) );
    }

    /**
     * Testa interface do método GetTrace.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetTrace
     */
    public function testGetTrace($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getTrace', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetTrace.
     */
    public function publicInterfaceGetTrace()
    {
        return array( array( ResultAsserterFactory::create(function($test, $obj, $result){
            $test->assertTrue(\is_array($result));
        }) , null ) );
    }

    /**
     * Testa interface do método GetPrevious.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetPrevious
     */
    public function testGetPrevious($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getPrevious', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetPrevious.
     */
    public function publicInterfaceGetPrevious()
    {
        return array( array( new \Exception('causa') , null ) );
    }

    /**
     * Testa interface do método GetTraceAsString.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetTraceAsString
     */
    public function testGetTraceAsString($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getTraceAsString', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetTraceAsString.
     */
    public function publicInterfaceGetTraceAsString()
    {
        return array( array( ResultAsserterFactory::create(function ($test, $obj, $result){
            $test->assertTrue(\preg_match('/^#0\s/', $result)===1);
        }) , null ) );
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
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, '__toString', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método __toString.
     */
    public function publicInterface__toString()
    {
        return array( array( ResultAsserterFactory::create(function ($test, $obj, $result){
            $test->assertTrue(\preg_match('/^exception \'Exception\' with message \'causa\' in\s/', $result)===1);
        }) , null ) );
    }

}
