<?php

namespace Commons\Pattern\Service\Impl;

use Commons\Pattern\Di\LookupManager;
use Psr\Log\LoggerInterface;

/**
 * Representa abstração para comportar a criação de composição de dados
 * na camada de persistência.
 */
abstract class AbstractDataCompositeService extends AbstractCoreService
{
    /**
     * @var mixed
     */
    private $dataManager;

    /**
     * Construtor padrão.
     *
     * @param mixed $dataManager Representa o gerenciador de dados.
     * @param LookupManager $lookupManager Representa o gerenciador de objetos para injeção de dependência.
     * @param LoggerInterface $logger Representa a interface de logger.
     */
    public function __construct($dataManager, LookupManager $lookupManager, LoggerInterface $logger)
    {
        parent::__construct($lookupManager, $logger);
        $this->dataManager = $dataManager;
    }

    /**
     * Recupera o gerenciador de dados.
     *
     * @return mixed
     */
    protected function getDataManager()
    {
        return $this->dataManager;
    }
}
