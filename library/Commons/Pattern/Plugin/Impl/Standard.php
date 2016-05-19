<?php

namespace Commons\Pattern\Plugin\Impl;

use Commons\Pattern\Plugin\Plugin;
use Commons\Pattern\Plugin\Dispatcher;
use Commons\Pattern\Plugin\Context;

/**
 *
 * @category Commons
 * @package Commons\Pattern\Plugin\Impl
 */
abstract class Standard implements Plugin
{

    /**
     *
     * @var \Commons\Pattern\Plugin\Dispatcher
     */
    protected $dispatcher;

    /**
     *
     * @var string
     */
    protected $prefix = 'standard_plugin';


    /* (non-PHPdoc)
     * @see \Commons\Pattern\Plugin\Plugin::setDispatcher()
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     *
     * @param \Commons\Pattern\Plugin\Context $context
     * @param string $prefix
     * @return string
     */
    protected function generateContextId(Context $context, $prefix = null)
    {
        if (null == $prefix) {
            $prefix = $this->prefix;
        }
        $operation = $context->getOperation();
        $params = serialize($context->getParams());
        return $prefix . '_' . sha1($operation . '_' . $params);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::onErrorDispatch()
     */
    public function errorDispatch(Context $context)
    {
        $context->rethrowException();
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::finallyDispatch()
     */
    public function finallyDispatch(Context $context)
    {
    }
}
