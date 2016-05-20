<?php

namespace CommonsTest\Pattern\Transaction\Mock;

use Commons\Pattern\Transaction\Annotation as TX;
use Commons\Pattern\Meta\TAlterEgoAware;

class TransactionalMethod
{
    use TAlterEgoAware;

    /**
     * @TX\Transactional(unit="logger")
     * @return string
     */
    public function doSomething()
    {
        return 'Faz algo';
    }

    /**
     * @TX\Transactional(unit="logger")
     * @throws \Exception
     */
    public function doSomethingWrong()
    {
        throw new \Exception('error');
    }

    /**
     * @TX\Transactional(unit="compositeLogger")
     * @return string
     */
    public function doSomethingComposite()
    {
        return "OpComposite[".$this->alterThis()->doSomethingOnTx1().",".$this->alterThis()->doSomethingOnTx2()."]";
    }

    /**
     * @TX\Transactional(unit="logger")
     * @return string
     */
    protected function doSomethingOnTx1()
    {
        return "OpTx1";
    }

    /**
     * @TX\Transactional(unit="logger2")
     * @return string
     */
    protected function doSomethingOnTx2()
    {
        return "OpTx2";
    }

    public function doSomethingWithoutTransaction()
    {
        return 'No Transaction';
    }

}
