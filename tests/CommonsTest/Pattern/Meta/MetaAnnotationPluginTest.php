<?php

namespace CommonsTest\Pattern\Meta;

use Commons\Pattern\Meta\Annotation as META;
use Commons\Pattern\Meta\MetaPluggablesBuilder;
use Commons\Pattern\Plugin\Impl\PluggableDecorator;
use Commons\Pattern\Meta\Plugin\MetaAnnotation;
use Commons\Pattern\Meta\MetaObject;
use Commons\Pattern\Meta\TAlterEgoAware;
use Commons\Pattern\Plugin\AnnotatedContext;

/**
 * @META\OnException("exceptionFromClassSayHello")
 */
class MetaAnnotationPluginTest extends \PHPUnit_Framework_TestCase
{
    use TAlterEgoAware;

    /**
     * @META\After("modifyPropertySettings")
     */
    private $property = null;

    private function modifyPropertySettings(AnnotatedContext $context)
    {
        $context->setReturn($context->getReturn() . ' modified.');
    }

    /**
     * @META\Before("beforeSayHello")
     * @META\After("afterSayHello")
     * @META\Last("finalSayHello")
     */
    protected function sayHello()
    {
        return "Hello";
    }

    /**
     * @META\Before("beforeSayHello")
     * @META\After("corruptedSayHello")
     * @META\OnException("exceptionSayHello")
     * @META\Last("finalSayHello")
     */
    protected function sayHelloWithError()
    {
        // a funcionalidade do objeto sem acesso via alterThis não é modificada.
        return $this->sayHello();
    }

    /**
     * @META\After("corruptedSayHello")
     */
    protected function sayHelloWithErrorResolvedByClassAnnotation()
    {
        return $this->sayHello();
    }

    private function beforeSayHello(AnnotatedContext $context)
    {
        self::assertTrue(\in_array($context->getOperation(), array('sayHelloWithError', 'sayHello')));
    }

    private function afterSayHello(AnnotatedContext $context)
    {
        $context->setReturn($context->getReturn() . " World!!");
    }

    private function corruptedSayHello(AnnotatedContext $context)
    {
        throw new \Exception($context->getReturn() . " Ruined World :(.");
    }

    private function exceptionSayHello(AnnotatedContext $context)
    {
        $context->setReturn($context->getException()->getMessage() . " Dont worry!!");
    }

    private function exceptionFromClassSayHello(AnnotatedContext $context)
    {
        $context->setReturn($context->getException()->getMessage() . " Dont worry from Class Annotation!!");
    }

    private function finalSayHello(AnnotatedContext $context)
    {
        $context->setReturn($context->getReturn() . " Everything will be fine!");
    }

    public function setUp()
    {
        $pluggables = new MetaPluggablesBuilder();
        $decorator = new PluggableDecorator(null, array('annotation'=> new MetaAnnotation()));
        $pluggables->setCallPluggable($decorator);
        $pluggables->setPropertySetterPluggable($decorator);

        // nesse cenário a classe conhece seu alter-ego, acessado via alterThis
        new MetaObject($this, $pluggables, false);
    }

    public function testMetaAnnotationEmPropriedade()
    {
        self::assertEquals(null, $this->alterThis()->property);
        $this->alterThis()->property = "Property";
        self::assertEquals('Property modified.', $this->alterThis()->property);
    }

    public function testMetaAnnotationEmMetodo()
    {
        self::assertEquals("Hello", $this->sayHello());
        self::assertEquals("Hello World!! Everything will be fine!", $this->alterThis()->sayHello());
    }

    public function testMetaAnnotationEmMetodoComErroCorrigido()
    {
        // perceba que a anotação no método sobrecreve a definição da anotação da classe.
        self::assertEquals("Hello", $this->sayHelloWithError());
        self::assertEquals("Hello Ruined World :(. Dont worry!! Everything will be fine!", $this->alterThis()->sayHelloWithError());
    }

    public function testMetaAnnotationEmMetodoComErroCorrigidoHandlerDaClasse()
    {
        self::assertEquals("Hello", $this->sayHelloWithErrorResolvedByClassAnnotation());
        self::assertEquals("Hello Ruined World :(. Dont worry from Class Annotation!!", $this->alterThis()->sayHelloWithErrorResolvedByClassAnnotation());
    }
}
