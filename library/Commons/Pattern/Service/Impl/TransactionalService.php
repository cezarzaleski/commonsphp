<?php

namespace Commons\Pattern\Service\Impl;

use Commons\Pattern\Transaction\Strategy\TransactionStrategy;
use Commons\Pattern\Transaction\Transactional;
use Commons\Pattern\Transaction\Demarcation;
use Commons\Pattern\Transaction\Transaction;
use Commons\Pattern\Di\LookupManager;
use Psr\Log\LoggerInterface;

/**
 * Serviço base para criação de blocos transacionais para os serviços
 * independente de tecnologias externas.
 */
class TransactionalService extends AbstractCoreService implements Transactional, Demarcation
{
    /**
     * @param string $strategyName
     */
    private $strategyName;

    /**
     * Construtor padrão.
     *
     * @param TransactionStrategy $strategy
     */
    public function __construct(TransactionStrategy $strategy, LookupManager $lookupManager, LoggerInterface $logger)
    {
        parent::__construct($lookupManager, $logger);
        $this->strategyName = $strategy->getName();
        Transaction::resolveTransactionActivation($strategy);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::beginTransaction()
     */
    public function beginTransaction()
    {
        return Transaction::beginTransaction($this->strategyName);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::rollback()
     */
    public function rollback()
    {
        return Transaction::rollback();
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::commit()
     */
    public function commit()
    {
        return Transaction::commit();
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::close()
     */
    public function close()
    {
        Transaction::getStrategy($this->strategyName)->close();
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Demarcation::demarcate()
     */
    public function demarcate($callback, array $args = array())
    {
        return Transaction::demarcate($this->strategyName, $callback, $args);
    }
}
