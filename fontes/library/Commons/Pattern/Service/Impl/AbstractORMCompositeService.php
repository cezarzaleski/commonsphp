<?php

namespace Commons\Pattern\Service\Impl;

use Commons\Pattern\Di\LookupManager;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

/**
 * Representa serviço para realizar composição de dados.
 */
abstract class AbstractORMCompositeService extends AbstractDataCompositeService
{

    /**
     * Construtor padrão.
     *
     * @param EntityManager $dataManager
     * @param LookupManager $lookupManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $dataManager, LookupManager $lookupManager, LoggerInterface $logger)
    {
        parent::__construct($dataManager, $lookupManager, $logger);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Service\Impl\AbstractDataCompositeService::getDataManager()
     * @return EntityManager
     */
    protected function getDataManager()
    {
        return parent::getDataManager();
    }
}
