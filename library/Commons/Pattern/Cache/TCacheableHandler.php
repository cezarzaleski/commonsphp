<?php

namespace Commons\Pattern\Cache;

/**
 * Trait que define mecanismos básicos para definir Cache em um objeto.
 */
trait TCacheableHandler
{
    /**
     * Cache para dados.
     *
     * @var \Commons\Pattern\Cache\Cache
     */
    protected $cache;

    /**
     * Define o cache da aplicação.
     *
     * @param \Commons\Pattern\Cache\Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Retorna o cache definido para o cliente.
     *
     * @return null|\Commons\Pattern\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache;
    }
}
