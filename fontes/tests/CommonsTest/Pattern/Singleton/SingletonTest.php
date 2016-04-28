<?php

namespace CommonsTest\Pattern\Singleton;

class SingletonTest extends \PHPUnit_Framework_TestCase
{
    use \Commons\Pattern\Singleton\TSingleton;

    /**
     * Testa o trait TSingleton.
     */
    public function testGetInstance() {
        // Recupera a única instância da classe.
        $instance = self::getInstance();

        // retorna a mesma instância independente de quantas chamadas
        $this->assertEquals($instance, $this->getInstance());

        // É da instância da mesma classe de teste que usa o trait.
        $this->assertInstanceOf('\CommonsTest\Pattern\Singleton\SingletonTest', $instance);
    }
}