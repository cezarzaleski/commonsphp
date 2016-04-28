<?php
namespace CommonsTest\Pattern\Transaction\Mock;

use Zend\Db\Adapter\Adapter;
use Commons\Pattern\Transaction\TransactionException;

class FailAdapterMock extends Adapter
{
    public function getDriver()
    {
        $callers= \debug_backtrace();
        if ($callers[1]['function'] == 'commit') {
            throw new TransactionException("Erro desconhecido no commit", 0, null);
        } else if ($callers[1]['function'] == 'rollback') {
            parent::getDriver()
            ->getConnection()
            ->rollback();
            throw new TransactionException("Erro desconhecido no rollback", 0, null);
        } else {
            return parent::getDriver();
        }
    }
}
