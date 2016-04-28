<?php

namespace Commons\Pattern\Cache;

use Zend\Cache\Storage\StorageInterface;

/**
 * Classe responsável por encapsular caches do Zend.
 */
class CacheZendAdapter implements Cache
{

    /**
     * @var \Zend\Cache\Storage\StorageInterface
     */
    private $cacheReference;

    /**
     * Construtor padrão.
     *
     * @param StorageInterface $zendAdapter Instância de um adaptador de cache do Zend.
     */
    public function __construct(StorageInterface $zendAdapter)
    {
        $this->cacheReference = $zendAdapter;
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Cache\Cache::add()
     */
    public function add($name, $value)
    {
        return $this->cacheReference->addItem($name, $value);
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Cache\Cache::set()
     */
    public function set($name, $value)
    {
        return $this->cacheReference->setItem($name, $value);
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Cache\Cache::get()
     */
    public function get($name)
    {
        return $this->cacheReference->getItem($name);
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Cache\Cache::remove()
     */
    public function remove($name)
    {
        return $this->cacheReference->removeItem($name);
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Cache\Cache::contains()
     */
    public function contains($name)
    {
        return $this->cacheReference->hasItem($name);
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Cache\Cache::clear()
     */
    public function clear()
    {
        return false;
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Cache\Cache::getTtl()
     */
    public function getTtl()
    {
        return $this->cacheReference->getOptions()->getTtl();
    }

    /**
     * Método responsável por retornar a instância do Cache original.
     *
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getRaw()
    {
        return $this->cacheReference;
    }
}
