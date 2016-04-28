<?php

namespace Commons\Pattern\Service\Impl;

use Commons\Pattern\Di\LookupManager;
use Commons\Pattern\Service\Service;
use Zend\Di\LocatorInterface;

/**
 * Representar a versão Zend do LookupManager para serviços.
 */
class ZendServiceLookupManager extends AbstractLookupManager
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    public function __construct(LocatorInterface $locator)
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
