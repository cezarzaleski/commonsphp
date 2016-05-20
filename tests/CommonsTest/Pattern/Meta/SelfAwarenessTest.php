<?php

namespace CommonsTest\Pattern\Meta;

use CommonsTest\Pattern\Meta\Mock\ObjetoConhecedorDeAlterEgo;
use Commons\Pattern\Meta\MetaObject;
use Commons\Pattern\Meta\MetaPluggablesBuilder;
use Commons\Pattern\Plugin\Impl\PluggableDecorator;
use CommonsTest\Pattern\Meta\Mock\PowerUpPlugin;

class SelfAwarenessTest extends \PHPUnit_Framework_TestCase
{
    public function testSelfAwarenessProprioAlterego()
    {
        $object = new ObjetoConhecedorDeAlterEgo();

        // primeiro cenário: Alter ego é o próprio objeto.
        self::assertEquals($object, $object->getAlter());
        self::assertEquals(10, $object->getForca());
        self::assertEquals(10, $object->getAlter()->getForca());
        self::assertEquals("Possuo 10 de força, meu alter ego possui 10 de força.", $object->descreverForca());
    }

    public function testSelfAwarenessOutroAlterEgo()
    {
        $object = new ObjetoConhecedorDeAlterEgo();

        $decorator = new PluggableDecorator(null, array('powerUpBy10' => new PowerUpPlugin(10)));
        $builder = new MetaPluggablesBuilder();
        $builder->addPluggable('getForca', $decorator);

        $meta = new MetaObject($object, $builder);

        // segundo cenário: Alter ego é um meta objeto.
        self::assertEquals($meta, $object->getAlter());
        self::assertEquals($meta, $meta->getAlter());
        self::assertEquals(10, $object->getForca());
        self::assertEquals(100, $meta->getForca());
        self::assertEquals(100, $object->getAlter()->getForca());
        self::assertEquals("Possuo 10 de força, meu alter ego possui 100 de força.", $object->descreverForca());
    }

    public function testSelfAwarenessSubstituicaoAlterEgo()
    {
        $criarSuperObjeto = function ($object, $power) {
            $decorator = new PluggableDecorator(null, array('super' => new PowerUpPlugin($power)));
            $builder = new MetaPluggablesBuilder();
            $builder->addPluggable('getForca', $decorator);

            return new MetaObject($object, $builder);
        };

        $object = new ObjetoConhecedorDeAlterEgo();

        // terceiro cenário: Mudança de Alter ego de um objeto.
        // OBS: Esse cenário é mais perigoso de utilizar, deve ser utilizado com muita cautela.

        // primeiro alterego com 1000 vezes a força normal
        $hercules = $criarSuperObjeto($object, 1000);

        self::assertEquals(10, $object->getForca());
        self::assertEquals($hercules, $object->getAlter());
        self::assertEquals($hercules, $hercules->getAlter());
        self::assertEquals(10000, $hercules->getForca());
        self::assertEquals(10000, $object->getAlter()->getForca());
        self::assertEquals("Possuo 10 de força, meu alter ego possui 10000 de força.", $object->descreverForca());

        // segundo alterego com 1000000 vezes a força normal
        $zeus = $criarSuperObjeto($object, 1000000);

        self::assertEquals(10, $object->getForca());
        self::assertEquals($zeus, $object->getAlter());
        self::assertEquals($zeus, $zeus->getAlter());
        self::assertEquals(10000000, $zeus->getForca());
        self::assertEquals(10000000, $object->getAlter()->getForca());
        self::assertEquals("Possuo 10 de força, meu alter ego possui 10000000 de força.", $object->descreverForca());
    }
}
