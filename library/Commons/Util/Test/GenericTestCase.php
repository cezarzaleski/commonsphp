<?php

namespace Commons\Util\Test;

class GenericTestCase extends \PHPUnit_Framework_TestCase
{

    public function assertPublicInterface($class, $method, $expectedResult, $params = array())
    {
        try {
            if (! $params) {
                $params = array();
            }
            // cria a classe
            $clazzInstance = $this->createClassInstance($class, $method, $params);
            $reflector = new \ReflectionClass($clazzInstance);

            // invoca o método
            $methodInvoker = $reflector->getMethod($method);
            if ($methodInvoker->isPublic()) {
                $return = null;
                    $return = $methodInvoker->invokeArgs($clazzInstance, $params);
                    if (is_subclass_of($expectedResult, 'Commons\Util\Test\ResultAsserter')) {
                        $expectedResult->assertResult($this, $clazzInstance, $return);
                    } else {
                        $this->assertEquals($expectedResult, $return);
                    }
            } else {
                throw new \ReflectionException('O método ' . $method . ' da classe ' . $class . ' não é público.');
            }
        } catch (\PHPUnit_Framework_Exception $phpunitException) {
            throw $phpunitException;
        } catch (\Exception $e) {
            if (is_subclass_of($expectedResult, '\Exception') ||
                (is_object($expectedResult) && get_class($expectedResult) === 'Exception')) {
                $this->assertInstanceOf(get_class($expectedResult), $e);
                if ($expectedResult->getMessage()) {
                    $this->assertEquals($expectedResult->getMessage(), $e->getMessage());
                }
            } else {
                $this->fail($e->getMessage());
            }
        }
    }

    /**
     * Cria uma instância da classe.
     * O método espera que exista um construtor padrão na classe,
     * caso contrário busca por um método que cria a instância
     * chamado de 'getInstance';
     *
     * @param mixed $class classe ou objeto
     * (o método corrige $class para classe se for objeto)
     * @return object
     */
    protected function createClassInstance(&$class, $method, $params)
    {
        $clazzInstance = $class;
        if (! is_object($clazzInstance)) {
            $reflector = new \ReflectionClass($clazzInstance);
            $constructor = $reflector->getConstructor();
            if (($constructor !== null && $constructor->isPublic()) || $reflector->isInstantiable()) {
                if ($method === '__construct') {
                    $clazzInstance = $reflector->newInstanceArgs($params);
                } else {
                    $clazzInstance = new $class();
                }
            } else {
                $invoker = $reflector->getMethod('getInstance');
                $clazzInstance = $invoker->invoke(null);
            }
        } else {
            $class = \get_class($class);
        }

        return $clazzInstance;
    }
}
