<?php

namespace Commons\Pattern\Transaction\Strategy;

use Zend\Db\Adapter\Adapter as ZendAdapter;

/**
 * Estratégia para utilização de transação dos adaptadores.
 */
class AdapterTransactionStrategy extends StandardTransactionStrategy
{

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    private $adapter;

    /**
     * Construtor padrão.
     *
     * @param ZendAdapter $adapter
     * @param string $name
     * @param \Commons\Pattern\Plugin\Pluggable $feature
     */
    public function __construct(ZendAdapter $adapter, $name = null, $feature = null)
    {
        $this->adapter = $adapter;
        parent::__construct($name, $feature);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Strategy\TransactionStrategy::beginTransaction()
     */
    public function beginTransaction()
    {
        $rawAdapter = $this->getAssigner();
        if ($rawAdapter && $rawAdapter->getDriver()) {
            $rawAdapter->getDriver()
                ->getConnection()
                ->beginTransaction();
        }
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Strategy\TransactionStrategy::rollback()
     */
    public function rollback()
    {
        $rawAdapter = $this->getAssigner();
        if ($rawAdapter && $rawAdapter->getDriver()) {
            $rawAdapter->getDriver()
                ->getConnection()
                ->rollBack();
        }
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Strategy\TransactionStrategy::commit()
     */
    public function commit()
    {
        $rawAdapter = $this->getAssigner();
        if ($rawAdapter && $rawAdapter->getDriver()) {
            $rawAdapter->getDriver()
                ->getConnection()
                ->commit();
        }
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Strategy\TransactionStrategy::close()
     */
    public function close()
    {
        $rawAdapter = $this->getAssigner();
        if ($rawAdapter && $rawAdapter->getDriver()) {
            $rawAdapter->getDriver()
                ->getConnection()
                ->disconnect();
        }
    }

    /**
     * Retorna o objeto delegador.
     *
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getAssigner()
    {
        return $this->adapter;
    }
}
