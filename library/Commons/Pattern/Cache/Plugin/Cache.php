<?php

namespace Commons\Pattern\Cache\Plugin;

use Commons\Pattern\Plugin\Plugin;
use Commons\Pattern\Plugin\Context;
use Commons\Pattern\Plugin\Impl\Selector;

/**
 * Plugin para Cache.
 *
 * @category Commons
 * @package Commons\Pattern\Cache\Plugin
 */
abstract class Cache extends Selector implements Plugin
{
    use \Commons\Pattern\Cache\TCacheableHandler;

    /**
     *
     * @var integer
     */
    protected $ttl = 0;

    /**
     *
     * @var string
     */
    protected $prefix = 'cache_plugin';

    /**
     *
     * @param integer $ttl
     * @return \Commons\Pattern\Plugin\Impl\Cache Provê interface fluente
     */
    public function enable($ttl = null)
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isEnable()
    {
        return null === $this->ttl || $this->ttl > 0;
    }

    /**
     *
     * @return integer
     */
    public function getTtl()
    {
        // se valor local do ttl é nulo, é retornado valor padrão do adaptador
        if (null === $this->ttl) {
            $cache = $this->isEnable() ? $this->getCache() : null;
            return $cache ? $cache->getTtl() : 0;
        }

        return $this->ttl;
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Plugin\Plugin::preDispatch()
     */
    public function preDispatch(Context $context)
    {
        if (!$this->isValidContext($context)) {
            return;
        }

        // captura cache
        $cache = $this->isEnable() ? $this->getCache() : null;

        if (! $cache) {
            return;
        }

        // captura dado do cache, quando disponível
        $id = $this->generateContextId($context, $this->prefix);
        if ($cache->contains($id)) {
            $context->setReturn($cache->get($id))
                ->setLock(true);
        }
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Plugin\Plugin::postDispatch()
     */
    public function postDispatch(Context $context)
    {
        // verifica se operação deve ser verificada
        if (!$this->isValidContext($context)) {
            return;
        }

        // captura cache
        $cache = $this->isEnable() ? $this->getCache() : null;
        if (! $cache) {
            return;
        }
        // grava dado no cache
        $id = $this->generateContextId($context, $this->prefix);
        $cache->set($id, $context->getReturn());
        $this->ttl = 0;
    }
}
