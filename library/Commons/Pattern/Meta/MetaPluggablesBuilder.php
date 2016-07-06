<?php

namespace Commons\Pattern\Meta;

use Commons\Pattern\Plugin\Pluggable;
use Commons\Pattern\Plugin\Impl\PluggablesBuilder;

/**
 * Classe responsável por guardar instâncias de repositórios de plugins para MetaObject.
 */
class MetaPluggablesBuilder extends PluggablesBuilder
{

    const CONSTRUCTOR = '__construct';
    const INVOKER = '__invoke';
    const PROPERTY_SET = '__set';
    const PROPERTY_GET = '__get';
    const CALL = '__call';

    /**
     * Adiciona repositório de plugins para operações.
     *
     * Intenciona criar mecanismo de interceptação para métodos mágicos __call e __callStatic.
     *
     * @param Pluggable $pluggable Instância do repositório de plugins.
     * @param string $staticScope [Opcional] caso necessite incorporar escopo estático declarar true.
     * @return \Commons\Pattern\Meta\MetaPluggablesBuilder Interface fluente.
     */
    public function setCallPluggable(Pluggable $pluggable)
    {
        $this->addPluggable(static::CALL, $pluggable);
        return $this;
    }

    /**
     * Insere um repositório de plugins para Set de Propriedades.
     *
     * Intenciona criar mecanismo de interceptação para método mágico __set.
     *
     * @param Pluggable $pluggable
     * @return \Commons\Pattern\Meta\MetaPluggablesBuilder
     */
    public function setPropertySetterPluggable(Pluggable $pluggable)
    {
        $this->addPluggable(static::PROPERTY_SET, $pluggable);
        return $this;
    }

    /**
     * Insere um repositório de plugins para Get de Propriedades.
     *
     * Intenciona criar mecanismo de interceptação para método mágico __get.
     *
     * @param Pluggable $pluggable
     * @return \Commons\Pattern\Meta\MetaPluggablesBuilder
     */
    public function setPropertyGetterPluggable(Pluggable $pluggable)
    {
        $this->addPluggable(static::PROPERTY_GET, $pluggable);
        return $this;
    }

    /**
     * Insere um repositório de plugins para Invoke.
     *
     * Intenciona criar mecanismo de interceptação para método mágico __invoke.
     *
     * @param Pluggable $pluggable
     * @return \Commons\Pattern\Meta\MetaPluggablesBuilder
     */
    public function setInvokerPluggable(Pluggable $pluggable)
    {
        $this->addPluggable(static::INVOKER, $pluggable);
        return $this;
    }

    /**
     * Insere um repositório de plugins para Construtor.
     *
     * Intenciona criar mecanismo de interceptação para método mágico __construct.
     *
     * @param Pluggable $pluggable
     * @return \Commons\Pattern\Meta\MetaPluggablesBuilder
     */
    public function setConstructorPluggable(Pluggable $pluggable)
    {
        $this->addPluggable(static::CONSTRUCTOR, $pluggable);
        return $this;
    }
}
