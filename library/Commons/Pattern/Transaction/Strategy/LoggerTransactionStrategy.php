<?php

namespace Commons\Pattern\Transaction\Strategy;

use Psr\Log\LoggerInterface;

/**
 * Estratégia apenas de marcação que realiza logs das operações de transação.
 * Pode ser utilizada em contextos de alto nível.
 */
class LoggerTransactionStrategy extends StandardTransactionStrategy
{
    /**
     * @var LoggerInterface
     */
    private $logger = null;

    /**
     * Construtor padrão.
     *
     * @param LoggerInterface $logger
     * @param string $name
     * @param \Commons\Pattern\Plugin\Pluggable $feature
     */
    public function __construct(LoggerInterface $logger, $name = null, $feature = null)
    {
        parent::__construct($name, $feature);
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::beginTransaction()
     */
    public function beginTransaction()
    {
        $this->logger->info('BEGINNING TRANSACTION UNIT=['.$this->getName().']');
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::rollback()
     */
    public function rollback()
    {
        $this->logger->info('ROLLBACKING TRANSACTION UNIT=['.$this->getName().']');
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::commit()
     */
    public function commit()
    {
        $this->logger->info('COMMITING TRANSACTION UNIT=['.$this->getName().']');
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::close()
     */
    public function close()
    {
        $this->logger->info('CLOSING TRANSACTION UNIT=['.$this->getName().'] CONNECTION');
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Delegate\Delegate::getAssigner()
     */
    public function getAssigner()
    {
        return $this->logger;
    }
}
