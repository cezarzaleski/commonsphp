<?php

namespace CommonsTest\Util\Annotation;

use Commons\Pattern\Cache\CacheZendAdapter;
use Commons\Pattern\Meta\Annotation\Last;
use Commons\Pattern\Meta\Annotation\OnException;
use Commons\Util\Annotation\AnnotationUtil;
use Commons\Util\Test\GenericTestCase;
use Commons\Util\Test\ResultAsserterFactory;
use Zend\Cache\Storage\Adapter\Memory;
use Commons\Exception\InvalidArgumentException;


/**
 * Função anotada.
 *
 * @\Commons\Pattern\Meta\Annotation\Last("nomethodfound")
 */
function test()
{
}

/**
 * Classe AnnotationUtilTest.
 *
 * @\Commons\Pattern\Meta\Annotation\OnException("nomethodfound")
 */
class AnnotationUtilTest extends GenericTestCase
{

    /**
     * @\Commons\Pattern\Meta\Annotation\Last("nomethodfound")
     *
     * @var string
     */
    public $test;

    /**
     * Método anotado.
     *
     * @\Commons\Pattern\Meta\Annotation\Last("nomethodfound")
     */
    public function annotatedMethod()
    {
    }

    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        return 'Commons\Util\Annotation\AnnotationUtil';
    }

    /**
     * Testa interface do método LoadAnnotationsFromNamespace.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceLoadAnnotationsFromNamespace
     */
    public function testLoadAnnotationsFromNamespace($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'loadAnnotationsFromNamespace', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método LoadAnnotationsFromNamespace.
     */
    public function publicInterfaceLoadAnnotationsFromNamespace()
    {
        $asserter = function ($namespace, $dir) {
            return ResultAsserterFactory::create(function($test, $obj, $result) use ($namespace, $dir){
                        $property = new \ReflectionProperty('Doctrine\Common\Annotations\AnnotationRegistry','autoloadNamespaces');
                        $property->setAccessible(true);
                        $arr = $property->getValue();
                        $test->assertEquals($dir, $arr[$namespace]);
                    });
        };

        return array(
                    array($asserter('?', '?'), array('?', '?')),
                    array($asserter('?', null), array('?', null)),
                    array($asserter(null, '?'), array(null, '?')),
                    array($asserter(null, null), array(null, null))
                );
    }

    /**
     * Testa interface do método ExtractAnnotations.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceExtractAnnotations
     */
    public function testExtractAnnotations($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'extractAnnotations', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método ExtractAnnotations.
     */
    public function publicInterfaceExtractAnnotations()
    {
        AnnotationUtil::loadAnnotationsFromNamespace('\Commons\Pattern\Meta\Annotation', __DIR__.'/../../../../library');
        $cache = new CacheZendAdapter(new Memory());

        $method = new \ReflectionMethod($this, 'annotatedMethod');
        return array(
                    array(ResultAsserterFactory::create(function($test, $obj, $result) use ($cache) {
                        $reflection = new \ReflectionFunction('\CommonsTest\Util\Annotation\test');
                        $test->assertEquals($result, AnnotationUtil::extractAnnotations($reflection, $cache));
                    }), array(new \ReflectionFunction('\CommonsTest\Util\Annotation\test'), $cache)),
                    array(ResultAsserterFactory::create(function($test, $obj, $result) {
                        $test->assertEquals('nomethodfound', $result[0]->value);
                    }), array(new \ReflectionFunction('\CommonsTest\Util\Annotation\test'), null)),
                    array(ResultAsserterFactory::create(function($test, $obj, $result){
                        $test->assertEquals('nomethodfound', $result[0]->value);
                    }), array($method, null)),
                    array(ResultAsserterFactory::create(function($test, $obj, $result){
                        $test->assertEquals('nomethodfound', $result[0]->value);
                    }), array(new \ReflectionProperty($this, 'test'), null)),
                    array(ResultAsserterFactory::create(function($test, $obj, $result){
                        $test->assertEquals('nomethodfound', $result[0]->value);
                    }), array(new \ReflectionObject($this), null))
                );
    }

    /**
     * Testa interface do método FindAnnotation.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceFindAnnotation
     */
    public function testFindAnnotation($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'findAnnotation', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método FindAnnotation.
     */
    public function publicInterfaceFindAnnotation()
    {
        $default = new Last();
        $annoOne = array(new Last());
        $annots = array(new Last(), new Last());
        $annoNone = array();
        $search = '\Commons\Pattern\Meta\Annotation\Last';
        $exception = new InvalidArgumentException(
                    'Its not allowed to use more than one '.$search.' annotation type in a method or function.'
        );
        $result = new Last();

        return array(
                    array($result, array($search, $annoOne, true, $default)),
                    array($exception, array($search, $annots, true, null)),
                    array($result, array($search, $annoOne, false, $default)),
                    array($result, array($search, $annots, false, null)),
                    array($default, array($search, $annoNone, true, $default)),
                    array(null, array($search, $annoNone, true, null)),
                    array($default, array($search, $annoNone, false, $default)),
                    array(null, array($search, $annoNone, false, null)),
                    array($default, array(null, $annoOne, true, $default)),
                    array(null, array(null, $annots, true, null)),
                    array($default, array(null, $annoOne, false, $default)),
                    array(null, array(null, $annots, false, null)),
                    array($default, array(null, $annoNone, true, $default)),
                    array(null, array(null, $annoNone, true, null)),
                    array($default, array(null, $annoNone, false, $default)),
                    array(null, array(null, $annoNone, false, null))
                );
    }
}
