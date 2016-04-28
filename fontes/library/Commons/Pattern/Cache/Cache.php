<?php

namespace Commons\Pattern\Cache;

/**
 * Interface responsável por abstrair o uso de Cache.
 */
interface Cache
{
    /**
     * Somente guarda o valor $value na chave $name se a chave não existir.
     *
     * @param string $name Nome da chave.
     * @param mixed $value Objeto.
     * @return bool
     */
    public function add($name, $value);

    /**
     * Sempre grava o valor $value na chave $name.
     *
     * @param string $name Nome da chave.
     * @param mixed $value Objeto.
     * @return bool
     */
    public function set($name, $value);

    /**
     * Retorna o valor guardado na chave $name.
     *
     * @param string $name Nome da chave.
     * @return mixed
     */
    public function get($name);

    /**
     * Remove o valor guardado na chave $name.
     *
     * @param string $name Nome da chave.
     * @return bool
     */
    public function remove($name);

    /**
     * Verifica se a chave $name existe no cache.
     *
     * @param string $name Nome da chave.
     * @return bool
     */
    public function contains($name);

    /**
     * Retorna o tempo de vida definido para a permanencia de valores.
     *
     * @return int|float
     */
    public function getTtl();

    /**
     * Limpa todos os dados do cache.
     *
     * @return bool
     */
    public function clear();
}
