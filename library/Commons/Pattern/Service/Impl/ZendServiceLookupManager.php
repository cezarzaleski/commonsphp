<?php

namespace Commons\Pattern\Service\Impl;

use Commons\Pattern\Di\LookupManager;
use Commons\Pattern\Service\Service;

/**
 * Representar a versão Zend do LookupManager para serviços.
 */
class ZendServiceLookupManager extends AbstractLookupManager
{
    /**
     * @var mixed
     */
    private $locator;

    public function __construct($locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Service\Impl\AbstractLookupManager::realGet()
     */
    protected function realGet($name, array $params = array())
    {
        return $this->locator->get($name, $params);
    }
}
