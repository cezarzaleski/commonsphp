<?php

namespace CommonsTest\Pattern\Cache\Plugin;

use Commons\Util\Test\GenericTestCase;
use Commons\Util\Test\ResultAsserterFactory;
use Commons\Pattern\Cache\CacheZendAdapter;
use Zend\Cache\Storage\Adapter\Memory;
use Commons\Exception\InvalidArgumentException;
use Commons\Util\Test\ResultAsserter;
use PhpParser\PrettyPrinter\Standard;
use Commons\Pattern\Cache\Plugin\StandardCachePlugin;
use Commons\Pattern\Plugin\Context;
use Commons\Pattern\Plugin\Dispatcher;

/**
 * Classe StandardCachePluginTest.
 */
class StandardCachePluginTest extends GenericTestCase
{
    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        return new StandardCachePlugin('/.*/');
    }

    public function createDefinitionWithCache()
    {
        $cache = new CacheZendAdapter(new Memory());
        return new StandardCachePlugin('/.*/', $cache);
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
        $classDef = 'Commons\Pattern\Cache\Plugin\StandardCachePlugin';
        $this->assertPublicInterface($classDef, '__construct', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método __construct.
     */
    public function publicInterface__construct()
    {
        $memory = new Memory();
        $cache = new CacheZendAdapter($memory);
        $exception = new InvalidArgumentException('O cache deve ser do tipo \Commons\Pattern\Cache\Cache.');
        return array(
                    array(null, array('/.*/', $cache)),
                    array(null, array('/.*/', null)),
                    array(null, array(null, $cache)),
                    array(null, array(null, null)),
                    array($exception, array(null, $memory)),
                );
    }

    /**
     * Testa interface do método SetCache.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceSetCache
     */
    public function testSetCache($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'setCache', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método SetCache.
     */
    public function publicInterfaceSetCache()
    {
        $memory = new Memory();
        $cache = new CacheZendAdapter($memory);
        return array(
                    array(ResultAsserterFactory::create(function($test, $obj, $result) use ($cache) {
                        $test->assertEquals($cache, $obj->getCache());
                    }), array($cache))
                );
    }

    /**
     * Testa interface do método GetCache.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetCache
     */
    public function testGetCache($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getCache', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetCache.
     */
    public function publicInterfaceGetCache()
    {
        return array( array( null , null ) );
    }

    /**
     * Testa interface do método Enable.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceEnable
     */
    public function testEnable($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'enable', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método Enable.
     */
    public function publicInterfaceEnable()
    {
        return array(
                    array(ResultAsserterFactory::create(function($test, $obj, $result){
                        // fluent interface
                        $test->assertEquals($obj, $result);
                        $test->assertEquals(1, $obj->getTtl());
                    }), array(1)),
                    array(ResultAsserterFactory::create(function($test, $obj, $result){
                        // fluent interface
                        $test->assertEquals($obj, $result);
                        $test->assertEquals(null, $obj->getTtl());
                    }), array(null))
                );
    }

    /**
     * Testa interface do método IsEnable.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceIsEnable
     */
    public function testIsEnable($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'isEnable', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método IsEnable.
     */
    public function publicInterfaceIsEnable()
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
        return array( array( null , null ) );
    }

    /**
     * Testa interface do método PreDispatch.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfacePreDispatch
     */
    public function testPreDispatch($expectedResult, $params)
    {
        $classDef = $this->createDefinitionWithCache();
        $this->assertPublicInterface($classDef, 'preDispatch', $expectedResult, $params);
    }

    public function operacaoQualquer()
    {
    }

    /**
     * Provedor de dados com a combinatória de testes para o método PreDispatch.
     */
    public function publicInterfacePreDispatch()
    {
        $context = new Context(null, $this, 'operacaoQualquer');
        return array(
                    array(null, array($context))
                );
    }

    /**
     * Testa interface do método PostDispatch.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfacePostDispatch
     */
    public function testPostDispatch($expectedResult, $params)
    {
        $classDef = $this->createDefinitionWithCache();
        $this->assertPublicInterface($classDef, 'postDispatch', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método PostDispatch.
     */
    public function publicInterfacePostDispatch()
    {
        $context = new Context(null, $this, 'operacaoQualquer');
        return array(
                    array(null, array($context))
                );

    }

    /**
     * Testa interface do método IsValidContext.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceIsValidContext
     */
    public function testIsValidContext($expectedResult, $params)
    {
        $classDef = $this->createDefinitionWithCache();
        $this->assertPublicInterface($classDef, 'isValidContext', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método IsValidContext.
     */
    public function publicInterfaceIsValidContext()
    {
        $context = new Context(null, $this, 'operacaoQualquer');
        return array(
                    array(true, array($context))
                );

    }

    /**
     * Testa interface do método SetDispatcher.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceSetDispatcher
     */
    public function testSetDispatcher($expectedResult, $params)
    {
        $classDef = $this->createDefinitionWithCache();
        $this->assertPublicInterface($classDef, 'setDispatcher', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método SetDispatcher.
     */
    public function publicInterfaceSetDispatcher()
    {
        $dispatcher = new Dispatcher();
        return array(
                    array(null, array($dispatcher))
                );

    }

    /**
     * Testa interface do método ErrorDispatch.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceErrorDispatch
     */
    public function testErrorDispatch($expectedResult, $params)
    {
        $classDef = $this->createDefinitionWithCache();
        $this->assertPublicInterface($classDef, 'errorDispatch', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método ErrorDispatch.
     */
    public function publicInterfaceErrorDispatch()
    {
        $context = new Context(null, $this, 'operacaoQualquer');
        return array(
                    array(null, array($context))
                );

    }

    /**
     * Testa interface do método FinallyDispatch.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceFinallyDispatch
     */
    public function testFinallyDispatch($expectedResult, $params)
    {
        $classDef = $this->createDefinitionWithCache();
        $this->assertPublicInterface($classDef, 'finallyDispatch', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método FinallyDispatch.
     */
    public function publicInterfaceFinallyDispatch()
    {
        $context = new Context(null, $this, 'operacaoQualquer');
        return array(
                    array(null, array($context))
                );

    }

}
