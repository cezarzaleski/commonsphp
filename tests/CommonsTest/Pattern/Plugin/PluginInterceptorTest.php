<?php

namespace CommonsTest\Pattern\Plugin;

use Commons\Pattern\Plugin\PluginInterceptor;
use CommonsTest\Pattern\Plugin\Mock\PluggableClass;
use CommonsTest\Pattern\Plugin\Mock\InvalidCachePluggableClass;

class PluginInterceptorTest extends \PHPUnit_Framework_TestCase
{
    public function testIntercept()
    {
        $var = 'teste';
        $pluggable = new PluggableClass();
        $result = PluginInterceptor::intercept($pluggable, $pluggable, 'cacheFunction', array($var));
        $this->assertEquals('Result teste alterado ', $result);
        $this->assertEquals(1, $pluggable->verifier);

        // Sem usar o plugin de fato o verificador será incrementado
        PluginInterceptor::intercept($pluggable, $pluggable, 'cacheFunction', array($var));
        $this->assertEquals(2, $pluggable->verifier);

        // Ativando o plugin de cache o verificador será incrementado uma última vez para 3.
        // Enable só para o escopo do método, para esse plugin o cache deve ser ativado novamente.
        $pluggable->getPluginDispatcher()->getPlugin('cache')->enable();
        PluginInterceptor::intercept($pluggable, $pluggable, 'cacheFunction', array($var));
        $this->assertEquals('Result teste alterado ', $result);
        $this->assertEquals(3, $pluggable->verifier);

        // E o verificador permanecerá 3 pois está sendo utilizado o cache.
        $pluggable->getPluginDispatcher()->getPlugin('cache')->enable();
        PluginInterceptor::intercept($pluggable, $pluggable, 'cacheFunction', array($var));
        $this->assertEquals(3, $pluggable->verifier);
    }
    
    /**
     * @expectedException Commons\Exception\InvalidArgumentException
     * @expectedExceptionMessage O cache deve ser do tipo \Commons\Pattern\Cache\Cache.
     */
    public function testInvalidCache()
    {
        $pluggable = new InvalidCachePluggableClass();
        $pluggable->getPluginDispatcher();
    }
}
