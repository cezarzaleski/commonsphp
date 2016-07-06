<?php

namespace Commons\Pattern\Singleton;

/**
 * Padrão de projeto Singleton para ser utilizado como Trait.
 */
trait TSingleton
{
    /**
     * Guarda o valor de um único objeto para uma classe.
     *
     * @var T Instância da classe.
     */
    private static $instance = null;

    /**
     * Retorna uma única instância da classe.
     *
     * @return T Classe que utiliza o trait.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}
