<?php

namespace Commons\Pattern\Multiton;

/**
 * Padrão de projeto Multiton nomeado para ser utilizado como Trait.
 */
trait TNamedMultiton
{
    /**
     * Guarda o valores únicos de objetos unicamente nomeados de uma classe.
     *
     * @var T[] Instância da classe.
     */
    protected static $instances = array();

    /**
     * Retorna uma instância única nomeada da classe.
     *
     * @param string $name Nome do objeto.
     * @return T Classe que utiliza o trait
     */
    public static function getInstance($name)
    {
        if (!isset(static::$instances[$name])) {
            static::$instances[$name] = new static($name);
        }
        return static::$instances[$name];
    }

    /**
     * Verifica se existe uma instância nomeada com o dado nome.
     *
     * @param string $name Nome do objeto.
     * @return bool true para se existe a instância nomeada, false caso contrário.
     */
    public static function hasInstance($name)
    {
        return array_key_exists($name, static::$instances);
    }

    /**
     * Destrói uma instância nomeado da classe.
     *
     * @param string $name Nome do objeto
     * @return bool true para se houve remoção, false caso contrário.
     */
    public static function destroyInstance($name)
    {
        $result = false;
        if (isset(static::$instances[$name])) {
            unset(static::$instances[$name]);
            $result = true;
        }
        return $result;
    }
}
