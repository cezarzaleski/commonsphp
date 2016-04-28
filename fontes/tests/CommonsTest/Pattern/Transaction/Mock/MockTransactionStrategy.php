<?php
namespace CommonsTest\Pattern\Transaction\Mock;

use Commons\Pattern\Transaction\Strategy\TransactionStrategy;
use Commons\Pattern\Transaction\Strategy\StandardTransactionStrategy;

class MockTransactionStrategy extends StandardTransactionStrategy implements TransactionStrategy
{

    const MOCK_TRANSACTION = 'string_work_mock';

    /**
     *
     * @var string Para propósitos de geração de erro.
     */
    private $alternativeName = null;

    private $assigner = null;

    public $work = null;

    public function setName($name)
    {
        $this->alternativeName = $name;
    }

    public function getName()
    {
        return ($this->alternativeName == null) ? MockTransactionStrategy::MOCK_TRANSACTION : $this->alternativeName;
    }

    public function beginTransaction()
    {
        $this->work = 'String Mocking';
    }

    public function rollback()
    {
        $this->work = null;
    }

    public function commit()
    {
        $this->work = $this->work . ' commiting...';
    }

    public function close()
    {
        $this->work = null;
    }

    public function setAssigner($assigner)
    {
        $this->assigner = $assigner;
    }

    public function getAssigner()
    {
        return $this->assigner;
    }
}
