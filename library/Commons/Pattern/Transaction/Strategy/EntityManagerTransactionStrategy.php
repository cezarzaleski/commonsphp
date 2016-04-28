<?php

namespace Commons\Pattern\Transaction\Strategy;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;

/**
 * Estratégia para utilização de transação de entidades (doctrine).
 */
class EntityManagerTransactionStrategy extends StandardTransactionStrategy
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Construtor padrão.
     *
     * @param DoctrineEntityManager $entityManager
     * @param string $name
     * @param \Commons\Pattern\Plugin\Pluggable $feature
     */
    public function __construct(DoctrineEntityManager $entityManager, $name = null, $feature = null)
    {
        $this->entityManager = $entityManager;
        parent::__construct($name, $feature);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Strategy\TransactionStrategy::beginTransaction()
     */
    public function beginTransaction()
    {
        $rawEntityManager = $this->getAssigner();
        $rawEntityManager->beginTransaction();
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Strategy\TransactionStrategy::rollback()
     */
    public function rollback()
    {
        $rawEntityManager = $this->getAssigner();
        $rawEntityManager->rollback();
        $rawEntityManager->clear();
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Strategy\TransactionStrategy::commit()
     */
    public function commit()
    {
        $rawEntityManager = $this->getAssigner();
        $rawEntityManager->flush();
        $rawEntityManager->commit();
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Strategy\TransactionStrategy::close()
     */
    public function close()
    {
        $this->getAssigner()->close();
    }

    /**
     * Retorna o objeto delegador.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getAssigner()
    {
        return $this->entityManager;
    }
}
