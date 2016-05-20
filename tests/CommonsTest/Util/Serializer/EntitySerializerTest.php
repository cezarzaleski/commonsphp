<?php

namespace CommonsTest\Util\Serializer;

use Commons\Util\Test\GenericTestCase;
use Commons\Util\Test\ResultAsserterFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Commons\Util\Serializer\EntitySerializer;
use CommonsTest\Pattern\Service\Mock\ExemploEntity;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Classe EntitySerializerTest.
 */
class EntitySerializerTest extends GenericTestCase
{

    protected static $em;

    public static function getEntityManager()
    {
        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__.'/../../Pattern/Service/Mock'), $isDevMode, null, null, false);

        $connectionOptions = array('driver' => 'pdo_sqlite', 'memory' => true);

        // obtaining the entity manager
        if (!static::$em) {
            static::$em =  EntityManager::create($connectionOptions, $config);
            $schemaTool = new SchemaTool(static::$em);

            $cmf = self::$em->getMetadataFactory();
            $classes = $cmf->getAllMetadata();

            $schemaTool->dropDatabase();
            $schemaTool->createSchema($classes);
        }
        return static::$em;
    }
    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        return new EntitySerializer($this->getEntityManager());
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
        $classDef = '\Commons\Util\Serializer\EntitySerializer';
        $this->assertPublicInterface($classDef, '__construct', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método __construct.
     */
    public function publicInterface__construct()
    {
        $em = $this->getEntityManager();
        return array(
                    array(ResultAsserterFactory::create(function ($test, $obj, $result) use ($em){
                        $test->assertEquals($em, $obj->getEntityManager());
                    }), array($this->getEntityManager()))
                );
    }

    /**
     * Testa interface do método GetEntityManager.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetEntityManager
     */
    public function testGetEntityManager($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getEntityManager', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetEntityManager.
     */
    public function publicInterfaceGetEntityManager()
    {
        return array( array( $this->getEntityManager() , null ) );
    }

    /**
     * Testa interface do método SetEntityManager.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceSetEntityManager
     */
    public function testSetEntityManager($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'setEntityManager', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método SetEntityManager.
     */
    public function publicInterfaceSetEntityManager()
    {
        $em = $this->getEntityManager();
        return array(
                    array(ResultAsserterFactory::create(function ($test, $obj, $result) use ($em){
                        $test->assertEquals($em, $obj->getEntityManager());
                    }), array($this->getEntityManager()))
                );
    }

    /**
     * Testa interface do método ToArray.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceToArray
     */
    public function testToArray($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'toArray', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método ToArray.
     */
    public function publicInterfaceToArray()
    {
        $entity = new ExemploEntity();
        $entities = array(new ExemploEntity(), new ExemploEntity());
        return array(
                    array(array('id'=>null, 'name'=>null), array($entity)),
                    array(array(
                        array('id'=>null, 'name'=>null),
                        array('id'=>null, 'name'=>null)
                    ), array($entities)),
                    array(null, array(null))
                );
    }

    /**
     * Testa interface do método ToJson.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceToJson
     */
    public function testToJson($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'toJson', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método ToJson.
     */
    public function publicInterfaceToJson()
    {
        $entity = new ExemploEntity();
        $entities = array(new ExemploEntity(), new ExemploEntity());

        return array(
                    array(\json_encode(array('id'=>null, 'name'=>null)), array($entity)),
                    array(\json_encode(array(
                        array('id'=>null, 'name'=>null),
                        array('id'=>null, 'name'=>null)
                    )), array($entities)),
                    array('null', array(null))
                );
    }

    /**
     * Testa interface do método ToXml.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceToXml
     */
    public function testToXml($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'toXml', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método ToXml.
     */
    public function publicInterfaceToXml()
    {
        $entity = new ExemploEntity();
        $entities = array(new ExemploEntity(), new ExemploEntity());

        return array(
                    array('<CommonsTest_Pattern_Service_Mock_ExemploEntity><collection name="aXRlbXNfMQ=="><item name="id"></item><item name="name"></item></collection></CommonsTest_Pattern_Service_Mock_ExemploEntity>', array($entity)),
                    array('<component><collection name="aXRlbXNfMQ=="><item name="0"><collection name="aXRlbXNfMg=="><item name="id"></item><item name="name"></item></collection></item><item name="1"><element ref="aXRlbXNfMg=="/></item></collection></component>', array($entities)),
                    array('<component></component>', array(null))
                );
    }

    /**
     * Testa interface do método SetMaxRecursionDepth.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceSetMaxRecursionDepth
     */
    public function testSetMaxRecursionDepth($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'setMaxRecursionDepth', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método SetMaxRecursionDepth.
     */
    public function publicInterfaceSetMaxRecursionDepth()
    {
        return array(
                    array(ResultAsserterFactory::create(function ($test, $obj, $result){
                        $test->assertEquals(3, $obj->getMaxRecursionDepth());
                    }), array(3)),
                    array(ResultAsserterFactory::create(function ($test, $obj, $result){
                        $test->assertEquals(null, $obj->getMaxRecursionDepth());
                    }), array(null))
                );
    }

    /**
     * Testa interface do método GetMaxRecursionDepth.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGetMaxRecursionDepth
     */
    public function testGetMaxRecursionDepth($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'getMaxRecursionDepth', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GetMaxRecursionDepth.
     */
    public function publicInterfaceGetMaxRecursionDepth()
    {
        return array( array( 0 , null ) );
    }

    public static function tearDownAfterClass()
    {
        self::$em = null;
    }
}
