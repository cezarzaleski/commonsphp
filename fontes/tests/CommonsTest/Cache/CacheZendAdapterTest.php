<?php

namespace CommonsTest\Pattern\Cache;

use Commons\Util\Test\GenericTestCase;
use Commons\Util\Test\ResultAsserterFactory;
use Commons\Pattern\Cache\CacheZendAdapter;
use Zend\Cache\Storage\Adapter\Memory;
use Zend\Cache\Exception\InvalidArgumentException;

/**
 * Classe CacheZendAdapterTest.
 */
class CacheZendAdapterTest extends GenericTestCase
{
    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        $zendAdapter = new Memory();
        return new CacheZendAdapter($zendAdapter);
    }

    /**
     * Testa interface do método Add.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceAdd
     */
    public function testAdd($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'add', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método Add.
     */
    public function publicInterfaceAdd()
    {
        $exception = new InvalidArgumentException("An empty key isn't allowed");
        return array(
                    array(true, array('teste', null)),
                    array(true, array('test', 'teste')),
                    array($exception, array(null, 'teste')),
                    array($exception, array(null, null))
                );
    }

    /**
     * Testa interface do método Set.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceSet
     */
    public function testSet($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'set', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método Set.
     */
    public function publicInterfaceSet()
    {
        $exception = new InvalidArgumentException("An empty key isn't allowed");
        return array(
                    array(true, array('teste', null)),
                    array(true, array('teste', 'teste')),
                    array($exception, array(null, 'teste')),
                    array($exception, array(null, null))
                );
    }

    /**
     * Testa interface do método Get.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGet
     */
    public function testGet($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $classDef->add('teste', 'teste');
        $this->assertPublicInterface($classDef, 'get', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método Get.
     */
    public function publicInterfaceGet()
    {
        $exception = new InvalidArgumentException("An empty key isn't allowed");
        return array(
                    array('teste', array('teste')),
                    array($exception, array(null))
                );
    }

    /**
     * Testa interface do método Remove.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceRemove
     */
    public function testRemove($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $classDef->add('teste', 'teste');
        $this->assertPublicInterface($classDef, 'remove', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método Remove.
     */
    public function publicInterfaceRemove()
    {
        $exception = new InvalidArgumentException("An empty key isn't allowed");
        return array(
                    array(true, array('teste')),
                    array($exception, array(null))
                );
    }

    /**
     * Testa interface do método Contains.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceContains
     */
    public function testContains($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $classDef->add('teste', 'teste');
        $this->assertPublicInterface($classDef, 'contains', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método Contains.
     */
    public function publicInterfaceContains()
    {
        $exception = new InvalidArgumentException("An empty key isn't allowed");
        return array(
                    array(true, array('teste')),
                    array($exception, array(null))
                );
    }

    /**
     * Testa interface do método Clear.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceClear
     */
    public function testClear($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'clear', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método Clear.
     */
    public function publicInterfaceClear()
    {
        return array( array( false , null ) );
    }

    /**
     * Testa interface do método GetTtl.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetTtl
     */
    public function testGetTtl($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getTtl', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetTtl.
     */
    public function publicInterfaceGetTtl()
    {
        return array( array( 0, null ) );
    }

    /**
     * Testa interface do método GetRaw.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetRaw
     */
    public function testGetRaw($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getRaw', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetRaw.
     */
    public function publicInterfaceGetRaw()
    {

        return array( array( ResultAsserterFactory::create(function ($testCase, $object, $result){
            return $testCase->assertTrue($result instanceof Memory);
        }) , null ) );
    }
}
