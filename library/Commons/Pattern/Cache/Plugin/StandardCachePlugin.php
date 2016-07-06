<?php

namespace Commons\Pattern\Cache\Plugin;

use Commons\Pattern\Cache\Plugin\Cache as CachePlugin;
use Commons\Pattern\Cache\Cache as CacheInterface;
use Commons\Exception\InvalidArgumentException;

/**
 * Plugin para Cache padrão.
 */
class StandardCachePlugin extends CachePlugin
{

    /**
     * Construtor padrão.
     *
     * @param string $regex Regex com string de pesquisa para métodos passíveis de serem cacheados.
     * @param \Commons\Pattern\Cache\Cache $cache
     */
    public function __construct($regex, $cache = null)
    {
        parent::__construct($regex);
        if (!is_null($cache) && !($cache instanceof \Commons\Pattern\Cache\Cache)) {
            throw new InvalidArgumentException("O cache deve ser do tipo \\Commons\\Pattern\\Cache\\Cache.");
        }
        $this->cache = $cache;
    }

    /**
     * @param \Commons\Pattern\Cache\Cache $cache
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @see \Commons\Pattern\Plugin\Impl\Cache::getCache()
     * @return \Commons\Pattern\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache;
    }
}
