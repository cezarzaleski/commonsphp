<?php

namespace CommonsTest\Pattern\Meta;

use CommonsTest\Pattern\Meta\Mock\ObjetoComplexo;
use Commons\Pattern\Meta\MetaObject;
use CommonsTest\Pattern\Meta\Mock\SuperPowerPluggable;
use Commons\Pattern\Meta\MetaPluggablesBuilder;
use Commons\Pattern\Meta\Meta;

class MetaObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorString()
    {
        $meta = new MetaObject('CommonsTest\Pattern\Meta\Mock\ObjetoComplexo', null, true, array('teste'));
        self::assertTrue($meta instanceof MetaObject);
        $this->assertObjetoPadrao($meta);
    }

    /**
     * @return \CommonsTest\Pattern\Meta\Mock\ObjetoComplexo
     */
    public function getObjetoComplexo()
    {
        return new ObjetoComplexo('teste');
    }

    /**
     * Através de injeção de dependência se passar pelo objeto real.
     *
     * @return \CommonsTest\Pattern\Meta\Mock\ObjetoComplexo
     */
    public function getMetaObjetoComplexo($obj, $builder = null)
    {
        return new MetaObject($obj, $builder);
    }

    public function testConstructor()
    {
        $obj = $this->getObjetoComplexo();
        $copy = clone $obj;
        $meta = $this->getMetaObjetoComplexo($obj);
        self::assertTrue($meta instanceof MetaObject);
        // o meta objeto deve ter o mesmo comportamento do objeto
        $this->assertObjetoPadrao($meta);
        // o objeto deve ter o mesmo comportamento.
        $this->assertObjetoPadrao($copy);
    }

    public function testClonedMeta()
    {
        $obj = $this->getObjetoComplexo();
        $meta = $this->getMetaObjetoComplexo($obj);
        $copy = clone $meta;
        self::assertTrue($copy instanceof MetaObject);
        $prop = new \ReflectionProperty($copy, 'wrapped');
        $prop->setAccessible(true);
        self::assertFalse($obj === $prop->getValue($copy));
        $this->assertObjetoPadrao($copy);
    }

    public function testSuperPowerOnMetaOperation()
    {
        $superPower = new SuperPowerPluggable();

        $builder = new MetaPluggablesBuilder();
        $builder->setCallPluggable($superPower);

        $obj = $this->getObjetoComplexo();
        $meta = $this->getMetaObjetoComplexo($obj, $builder);

        $meta->recuperarParametro(4, 4);
        self::assertEquals('Super pre teste 4 4 post final Spell.', $superPower->getSuperPower());

        $superPower->resetSuperPower();

        $meta->sayHello();
        self::assertEquals('Super pre Hello! post final Spell.', $superPower->getSuperPower());

        $superPower->resetSuperPower();

        try {
            $meta->lancaExcecao();
        } catch (\Exception $e) {
            self::assertEquals('Super pre Teste erro error final Spell.', $superPower->getSuperPower());
        }
    }

    public function testSuperPowerOnRecuperarParametroOperation()
    {
        $superPower = new SuperPowerPluggable();

        $builder = new MetaPluggablesBuilder();
        $builder->addPluggable('recuperarParametro', $superPower);


        $obj = $this->getObjetoComplexo();
        $meta = $this->getMetaObjetoComplexo($obj, $builder);

        $meta->recuperarParametro(4, 4);
        self::assertEquals('Super pre teste 4 4 post final Spell.', $superPower->getSuperPower());

        $superPower->resetSuperPower();

        $meta->sayHello();
        self::assertEquals('Super ', $superPower->getSuperPower());

        $superPower->resetSuperPower();

        try {
            $meta->lancaExcecao();
        } catch (\Exception $e) {
            self::assertEquals('Super ', $superPower->getSuperPower());
        }
    }

    public function testIsMeta()
    {
        $obj = $this->getObjetoComplexo();
        $meta = $this->getMetaObjetoComplexo($obj);
        self::assertTrue(Meta::isMeta($meta));
    }

    public function testMetaCast()
    {
        $obj = $this->getObjetoComplexo();
        $meta = $this->getMetaObjetoComplexo($obj);
        self::assertEquals($obj, Meta::unwrap($meta));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Object instance is not a MetaObject.
     */
    public function testMetaCastNotAMetaObject()
    {
        $obj = $this->getObjetoComplexo();
        Meta::cast($obj);
    }

    public function assertObjetoPadrao($obj)
    {
        $serial = \serialize($obj);
        self::assertEquals($obj, \unserialize($serial));
        self::assertEquals('teste 1 2', $obj->recuperarParametro(1, 2));
        self::assertEquals('Hello!', $obj->sayHello());
        self::assertTrue(isset($obj->propriedadePublica));
        $obj->propriedadePublica = 'string publica';
        self::assertEquals('string publica', $obj->propriedadePublica);
        $obj->propriedadePublica = 'outra';
        self::assertEquals('outra', $obj->propriedadePublica);
        unset($obj->propriedadePublica);
        self::assertFalse(isset($obj->propriedadePublica));
        self::assertEquals('Invoker', $obj());
        self::assertEquals('Objeto Complexo', (string)$obj);

        try {
            $obj->lancaExcecao();
        } catch (\Exception $e) {
            self::assertTrue($e instanceof \LogicException);
            self::assertEquals('Teste erro', $e->getMessage());
        }
    }
}
