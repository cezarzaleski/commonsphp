<?php

namespace CommonsTest\Pattern\Multiton;

class NamedMultitonTest extends \PHPUnit_Framework_TestCase
{
    use \Commons\Pattern\Multiton\TNamedMultiton;

    /**
     * Testa o trait TNamedMultiton.
     */
    public function testGetInstance() {
        // Recupera a única instância da classe.
        $instance = self::getInstance("teste");

        // retorna a mesma instância independente de quantas chamadas
        $this->assertEquals($instance, $this->getInstance("teste"));

        // É da instância da mesma classe de teste que usa o trait.
        $this->assertInstanceOf('\CommonsTest\Pattern\Multiton\NamedMultitonTest', $instance);
    }

    /**
     * Testa o destruição de uma instância do Multiton.
     */
    public function testDestroyInstance() {
        // Recupera a única instância da classe.
        $instance = self::getInstance("teste");
        
        // verifica se foi destruído.
        $this->assertTrue(self::destroyInstance("teste"));
    
        // retorna uma instância diferente (apenas o ponteiro) após destruição
        $this->assertFalse($instance === $this->getInstance("teste"));
    
        // É da instância da mesma classe de teste que usa o trait.
        $this->assertInstanceOf('\CommonsTest\Pattern\Multiton\NamedMultitonTest', $instance);
    }

    /**
     * Testa a verificação de instâncias do Multiton.
     */
    public function testHasInstance() {
        // Recupera a única instância da classe.
        self::getInstance("teste");
    
        // verifica se existe.
        $this->assertTrue(self::hasInstance("teste"));
        
        // verifica que não existe.
        $this->assertFalse(self::hasInstance("instancia.nunca.criada"));
    }    

    /**
     * Testa o destruição de uma instância do Multiton (falha).
     */
    public function testFailDestroyInstance() {
        // verifica que não foi destruído, pois não existia.
        $this->assertFalse(self::destroyInstance("instancia.nunca.criada"));
    }
}