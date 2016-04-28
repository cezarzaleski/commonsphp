<?php
namespace CommonsTest\Pattern\Service;

use Commons\Pattern\Transaction\Strategy\StandardTransactionStrategy;

class DummyTransactionStrategy extends StandardTransactionStrategy
{
    private $state;

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::beginTransaction()
     */
    public function beginTransaction()
    {
        $this->state = 'beginTransaction';
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::rollback()
     */
    public function rollback()
    {
        $this->state = 'rollback';
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::commit()
     */
    public function commit()
    {
        $this->state = 'commit';
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Transactional::close()
     */
    public function close()
    {
        $this->state = 'close';
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Delegate\Delegate::getAssigner()
     */
    public function getAssigner()
    {
        return null;
    }

    public function getState()
    {
        return $this->state;
    }

    public function cleanState()
    {
        $this->state = null;
    }
}
