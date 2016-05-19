<?php

namespace Commons\Pattern\Meta;

use Commons\Exception\InvalidArgumentException;
use Commons\Pattern\Plugin\Context;
use Commons\Pattern\Plugin\Pluggable;
use Commons\Pattern\Plugin\PluginInterceptor;
use Commons\Util\Reflect\ReflectionUtil;

/**
 * Meta objeto mágico capaz de se passar por um objeto real adicionando ao objeto real
 * interceptação de suas operações e capacidades adicionais.
 */
final class MetaObject
{
    /**
     * @var MetaPluggablesBuilder Repositório de plugins que executaram ao redor das operações e propriedades
     * do objeto real.
     */
    private $pluggables;

    /**
     * @var mixed Objeto real a ser envolvido.
     */
    private $wrapped;

    /**
     * Modo estrito proíbe a classe de acessar métodos protegidos e privados pelo metaobjeto.
     * @var boolean
     */
    private $strict = true;

    /**
     * Construtor padrão.
     *
     * @param MetaPluggablesBuilder $pluggables Repositório de plugins.
     * @param string|object $objectToWrap   Caso $objectToWrap seja uma string,
     * poderá ser passado o argumento opcional $constructorParams o que fará com que o próprio construtor
     * do objeto seja envolvido pelos plugins. Caso $objectToWrap seja um objeto o array $constructorParams
     * será desconsiderado.
     * @param boolean $strict Padrão true, false para acessar métodos protegidos e privados da classe pelo metaobjeto
     * dentro da própria classe.
     * @param array $constructorParams Array opcional apenas para o caso de $objectToWrap ser uma string
     * para auxiliar na invocação do método.
     *
     * @throws InvalidArgumentException
     *  Caso o objeto não seja string ou objeto, lança mensagem 'Tipo de objeto inválido para o MetaObject.'.
     */
    public function __construct(
        $objectToWrap,
        MetaPluggablesBuilder $pluggables = null,
        $strict = true,
        array $constructorParams = array()
    ) {
        $this->pluggables = $pluggables;
        if (\is_string($objectToWrap)) {
            $closure = function (Context $context) {
                $reflect = new \ReflectionClass($context->getOwner());
                return $reflect->newInstanceArgs($context->getParams());
            };
            $this->wrapped = $this->aroundClosure($objectToWrap, '__construct', $constructorParams, $closure);
        } elseif (\is_object($objectToWrap)) {
            $this->wrapped = $objectToWrap;
        } else {
            throw new InvalidArgumentException('Tipo de objeto inválido para o MetaObject.');
        }
        $this->strict = $strict;
        $this->metaAwarenessNormalization($this->wrapped);
    }

    /**
     * Normaliza um objeto que possua trait TAlterEgoAware.
     *
     * @param mixed $object
     */
    private function metaAwarenessNormalization($object)
    {
        $traits = \class_uses($object);
        if (isset($traits['Commons\Pattern\Meta\TAlterEgoAware'])) {
            $property = new \ReflectionProperty($object, 'alterThis');
            $property->setAccessible(true);
            $property->setValue($object, $this);
            $property->setAccessible(false);
        }
    }

    /**
     * Realiza operações ao redor de uma closure.
     *
     * @param mixed $owner
     * @param array $params
     * @param closure $closureAround
     * @param Pluggable $pluggable
     * @return NULL|mixed
     */
    private function aroundClosure(
        $owner,
        $operation,
        array $params,
        $closure,
        $operationType = Context::OP_METHOD,
        $pluggable = null
    ) {
        $result = null;

        if (!$pluggable) {
            $pluggable = $this->getPluggableMethod($operation);
        }

        $refParams = ReflectionUtil::getArrayReference($params);
        if ($pluggable) {
            $context = new Context($pluggable, $owner, $operation, $refParams, $operationType);
            $result = PluginInterceptor::interceptThroughClosure($context, $closure);
        } else {
            $context = new Context(null, $owner, $operation, $refParams, $operationType);
            $result = $closure($context);
        }
        return $result;
    }

    /**
     * Realiza operações ao redor de uma closure e também envolve essa closure em operações da metaoperação.
     * Para métodos globais essa operação é importante pois por exemplo em __call pode-se definir interceptações
     * de plugins para todos os métodos evitando a duplicação de plugins globais para cada método em particular.
     *
     * @param mixed $owner
     * @param string $operation
     * @param array $params
     * @param callback $closure
     * @param string $metaOperation
     * @param integer $opType
     * @return NULL|\Commons\Pattern\Meta\NULL|mixed
     */
    private function metaAroundClosure(
        $owner,
        $operation,
        array $params,
        $closure,
        $metaOperation,
        $opType = Context::OP_METHOD
    ) {
        $result = null;
        $pluggable = $this->getPluggableMethod($metaOperation);
        if ($pluggable) {
            $self = $this;
            $metaClosure = function () use ($self, $owner, $operation, $params, $closure, $opType) {
                return $self->aroundClosure($owner, $operation, $params, $closure, $opType);
            };
            $result = $this->aroundClosure($owner, $operation, $params, $metaClosure, $opType, $pluggable);
        } else {
            $result = $this->aroundClosure($owner, $operation, $params, $closure, $opType);
        }
        return $result;
    }

    /**
     * Recupera instância do repositório de plugins para método ou propriedade.
     *
     * @param string $name
     * @return Pluggable
     */
    private function getPluggableMethod($name)
    {
        return ($this->pluggables) ? $this->pluggables->getPluggable($name) : null;
    }

    /**
     * Verifica se a operação do objeto envolvido está sendo chamada internamente no mesmo.
     * Simula para o MetaObjeto o uso do escopo interno do objeto permitindo que propriedades e
     * métodos privados e protegidos sejam acessadas apenas nesse contexto e não externamente.
     *
     * @return boolean
     */
    private function isCallingMetaInsideWrappedObject()
    {
        $isInside = false;
        if ($this->strict !== true) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 5);
            foreach ($trace as $debug) {
                if (isset($debug['object']) && $debug['object'] === $this->wrapped) {
                    $isInside = true;
                    break;
                }
            }
        }
        return $isInside;
    }

    /**
     * Destrutor padrão.
     */
    public function __destruct()
    {
        $this->wrapped = null;
    }

    /**
     * Método que delega operações para todas as operações do objeto envolvido.
     *
     * @param string $name
     * @param array $arguments
     * @return NULL|mixed
     */
    public function __call($name, array $arguments)
    {
        $isInsideWrapped = $this->isCallingMetaInsideWrappedObject();
        $closure = function (Context $context) use ($isInsideWrapped) {
            $result = null;
            if ($isInsideWrapped) {
                $method = new \ReflectionMethod($context->getOwner(), $context->getOperation());
                $method->setAccessible(true);
                $result = $method->invokeArgs($context->getOwner(), $context->getParams());
            } else {
                $result = \call_user_func_array(
                    array(
                        $context->getOwner(),
                        $context->getOperation()
                    ),
                    $context->getParams()
                );
            }
            return $result;
        };
        return $this->metaAroundClosure($this->wrapped, $name, $arguments, $closure, '__call');
    }

    /**
     * Método que delega operações para todas as operações de get de propriedade do objeto envolvido.
     *
     * @param string $name
     * @return NULL|mixed
     */
    public function __get($name)
    {
        $isInsideWrapped = $this->isCallingMetaInsideWrappedObject();
        $closure = function (Context $context) use ($isInsideWrapped) {
            $owner = $context->getOwner();
            $operation = $context->getOperation();
            $result = null;
            if ($isInsideWrapped) {
                $property = new \ReflectionProperty($owner, $operation);
                $property->setAccessible(true);
                $result = $property->getValue($owner);
            } else {
                $result = $owner->$operation;
            }
            return $result;
        };
        return $this->metaAroundClosure($this->wrapped, $name, array(), $closure, '__get', Context::OP_PROPERTY);
    }

    /**
     * Método que delega operações para todas as operações de set de propriedade do objeto envolvido.
     *
     * @param string $name
     * @param mixed $value
     * @return NULL|mixed
     */
    public function __set($name, $value)
    {
        $isInsideWrapped = $this->isCallingMetaInsideWrappedObject();
        $closure = function (Context $context) {
            $params = $context->getParams();
            return $params[0];
        };
        $res = $this->metaAroundClosure($this->wrapped, $name, array($value), $closure, '__set', Context::OP_PROPERTY);
        if ($isInsideWrapped) {
            $property = new \ReflectionProperty($this->wrapped, $name);
            $property->setAccessible(true);
            $property->setValue($this->wrapped, $res);
        } else {
            $this->wrapped->$name = $res;
        }
        return $res;
    }

    /**
     * Método que delega verificação de isset de propriedade para objeto envolvido.
     *
     * @param string $name
     */
    public function __isset($name)
    {
        return isset($this->wrapped->$name);
    }

    /**
     * Método que delega remoção de propriedade para objeto envolvido.
     *
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->wrapped->$name);
    }

    /**
     * Método que delega chamada de __toString para objeto envolvido.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->wrapped;
    }

    /**
     * Método que delega chamada de __invoke para objeto envolvido.
     *
     * @return NULL|mixed
     */
    public function __invoke()
    {
        $params = \func_get_args();
        $closure = function (Context $context) {
            $owner = $context->getOwner();
            $params = $context->getParams();
            return $owner($params);
        };
        return $this->aroundClosure($this->wrapped, '__invoke', $params, $closure);
    }

    /**
     * Método responsável por realizar clonagem do metaobjeto.
     */
    public function __clone()
    {
        $this->wrapped = clone $this->wrapped;
    }
}
