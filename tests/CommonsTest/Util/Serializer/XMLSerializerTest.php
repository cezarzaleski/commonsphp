<?php

namespace CommonsTest\Util\Serializer;

use Commons\Util\Test\GenericTestCase;
use Commons\Util\Test\ResultAsserterFactory;

/**
 * Classe XMLSerializerTest.
 */
class XMLSerializerTest extends GenericTestCase
{
    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        return 'Commons\Util\Serializer\XMLSerializer';
    }

    /**
     * Testa interface do método GenerateValidXml.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGenerateValidXml
     */
    public function testGenerateValidXml($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'generateValidXml', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GenerateValidXml.
     */
    public function publicInterfaceGenerateValidXml()
    {
        $arr = array('prop'=>'teste');
        $blockName = 'grupo';
        $version = '2.0';
        $encoding = 'ISO-8859-1';


        return array(
                    array('<?xml version="2.0" encoding="ISO-8859-1" ?><grupo><collection name="aXRlbXNfMQ=="><item name="prop">teste</item></collection></grupo>',
                        array($arr, $blockName, $version, $encoding)),
                    array('<?xml version="2.0" encoding="UTF-8" ?><grupo><collection name="aXRlbXNfMQ=="><item name="prop">teste</item></collection></grupo>',
                        array($arr, $blockName, $version, null)),
                    array('<?xml version="1.0" encoding="ISO-8859-1" ?><grupo><collection name="aXRlbXNfMQ=="><item name="prop">teste</item></collection></grupo>',
                        array($arr, $blockName, null, $encoding)),
                    array('<?xml version="1.0" encoding="UTF-8" ?><grupo><collection name="aXRlbXNfMQ=="><item name="prop">teste</item></collection></grupo>',
                        array($arr, $blockName, null, null)),
                    array('<?xml version="2.0" encoding="ISO-8859-1" ?><component><collection name="aXRlbXNfMQ=="><item name="prop">teste</item></collection></component>',
                        array($arr, null, $version, $encoding)),
                    array('<?xml version="2.0" encoding="UTF-8" ?><component><collection name="aXRlbXNfMQ=="><item name="prop">teste</item></collection></component>',
                        array($arr, null, $version, null)),
                    array('<?xml version="1.0" encoding="ISO-8859-1" ?><component><collection name="aXRlbXNfMQ=="><item name="prop">teste</item></collection></component>',
                        array($arr, null, null, $encoding)),
                    array('<?xml version="1.0" encoding="UTF-8" ?><component><collection name="aXRlbXNfMQ=="><item name="prop">teste</item></collection></component>',
                        array($arr, null, null, null)),
                    array('<?xml version="2.0" encoding="ISO-8859-1" ?><grupo></grupo>',
                        array(null, $blockName, $version, $encoding)),
                    array('<?xml version="2.0" encoding="UTF-8" ?><grupo></grupo>',
                        array(null, $blockName, $version, null)),
                    array('<?xml version="1.0" encoding="ISO-8859-1" ?><grupo></grupo>',
                        array(null, $blockName, null, $encoding)),
                    array('<?xml version="1.0" encoding="UTF-8" ?><grupo></grupo>',
                        array(null, $blockName, null, null)),
                    array('<?xml version="2.0" encoding="ISO-8859-1" ?><component></component>',
                        array(null, null, $version, $encoding)),
                    array('<?xml version="2.0" encoding="UTF-8" ?><component></component>',
                        array(null, null, $version, null)),
                    array('<?xml version="1.0" encoding="ISO-8859-1" ?><component></component>',
                        array(null, null, null, $encoding)),
                    array('<?xml version="1.0" encoding="UTF-8" ?><component></component>',
                        array(null, null, null, null))
                );
    }

    /**
     * Testa interface do método GenerateXmlStructure.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceGenerateXmlStructure
     */
    public function testGenerateXmlStructure($expectedResult, $params)
    {
        $classDef = $this->createDefinition();
        $this->assertPublicInterface($classDef, 'generateXmlStructure', $expectedResult, $params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método GenerateXmlStructure.
     */
    public function publicInterfaceGenerateXmlStructure()
    {
        $arr = array('prop'=>'teste');
        return array(
                    array('<grupo><collection name="aXRlbXNfMQ=="><item name="prop">teste</item></collection></grupo>', array($arr, 'grupo')),
                    array('<component><collection name="aXRlbXNfMQ=="><item name="prop">teste</item></collection></component>', array($arr, null)),
                    array('<component><collection name="aXRlbXNfMQ=="><item name="prop">teste</item></collection></component>', array($arr)),
                    array('<grupo></grupo>', array(null,'grupo')),
                    array('<component></component>', array(null, null)),
                    array('<component></component>', array(null))
                );
    }

}
