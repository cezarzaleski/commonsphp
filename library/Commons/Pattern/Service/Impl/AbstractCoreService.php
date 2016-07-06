<?php

namespace Commons\Pattern\Service\Impl;

use Commons\Pattern\Di\LookupManager;
use Psr\Log\LoggerInterface;
use Commons\Pattern\Service\Service;

/**
 * Case base para criação de serviços.
 */
abstract class AbstractCoreService implements Service
{
    /**
     * @var LookupInterface
     */
    protected $lookupManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Construtor padrão.
     *
     * @param LookupInterface $lookupManager
     * @param LoggerInterface $logger
     */
    public function __construct(LookupManager $lookupManager, LoggerInterface $logger)
    {
        $this->lookupManager  = $lookupManager;
        $this->logger = $logger;
    }

    /**
     * @return \Commons\Pattern\Di\LookupManager
     */
    protected function getLookupManager()
    {
        return $this->lookupManager;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function getLogger()
    {
        return $this->logger;
    }
}
