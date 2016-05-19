<?php

namespace Commons\Pattern\Transaction\Plugin;

use Commons\Pattern\Cache\Cache;
use Commons\Pattern\Plugin\Context;
use Commons\Pattern\Plugin\Impl\AbstractAnnotationExtractor;
use Commons\Pattern\Transaction\Transaction;

/**
 * Demarca transações através de anotações.
 */
class Transactional extends AbstractAnnotationExtractor
{
    /**
     * Guarda as unidades de transação.
     * @var array
     */
    private $units = array();

    /**
     * Construtor padrão.
     */
    public function __construct(Cache $cache = null)
    {
        parent::__construct(
            array(
                'Commons\Pattern\Transaction\Annotation' => __DIR__ . '/../../../../../library'
            ),
            $cache
        );
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::preDispatch()
     */
    public function preDispatch(Context $context)
    {
        $transact = $this->findAnnotation($context, '\Commons\Pattern\Transaction\Annotation\Transactional', true);

        if ($transact !== null) {
            \array_unshift($this->units, $transact->unit);
            Transaction::beginTransaction($this->units[0]);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::postDispatch()
     */
    public function postDispatch(Context $context)
    {
        if (!empty($this->units) && Transaction::verifyActiveTransaction($this->units[0])) {
            Transaction::commit();
        }
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Impl\Standard::errorDispatch()
     */
    public function errorDispatch(Context $context)
    {
        if (!empty($this->units) && Transaction::verifyActiveTransaction($this->units[0])) {
            Transaction::rollback();
        }
        $context->rethrowException();
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Impl\Standard::finallyDispatch()
     */
    public function finallyDispatch(Context $context)
    {
        \array_shift($this->units);
    }
}
