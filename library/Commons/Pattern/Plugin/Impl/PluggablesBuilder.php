<?php

namespace Commons\Pattern\Plugin\Impl;

use Commons\Pattern\Plugin\Pluggable;

/**
 * Classe responsável por guardar instâncias de repositórios de plugins.
 */
class PluggablesBuilder
{
    /**
     * @var array
     */
    private static $staticPluggables = array();

    /**
     * @var array
     */
    private $instancePluggables = array();

    /**
     * Adiciona um repositório de plugins Pluggable.
     *
     * @param string $name Nome do repositório de plugins.
     * @param Pluggable $pluggable Instância do repositório de plugins.
     * @param boolean $staticScope [Opcional] caso necessite incorporar escopo estático declarar true.
     * @return PluggablesBuilder Interface fluente.
     */
    public function addPluggable($name, Pluggable $pluggable, $staticScope = false)
    {
        if ($staticScope) {
            static::$staticPluggables[$name] = $pluggable;
        } else {
            $this->instancePluggables[$name] = $pluggable;
        }

        return $this;
    }

    /**
     * Recupera um repositório de plugins Pluggable nomeado.
     *
     * @param string $name Nome do repositório de plugins.
     * @return NULL|Pluggable Repositório de plugins com o nome desejado.
     */
    public function getPluggable($name)
    {
        $pluggable = null;
        if (isset($this->instancePluggables[$name])) {
            $pluggable = $this->instancePluggables[$name];
        }
        return $pluggable;
    }

    /**
     * Recupera um repositório de plugins Pluggable nomeado do escopo estático.
     * @param string $name Nome do repositório de plugins.
     * @return NULL|Pluggable Repositório de plugins com o nome desejado.
     */
    public static function getStaticPluggable($name)
    {
        $pluggable = null;
        if (isset(static::$staticPluggables[$name])) {
            $pluggable = static::$staticPluggables[$name];
        }
        return $pluggable;
    }
}
