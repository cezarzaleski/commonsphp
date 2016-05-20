<?php

namespace CommonsTest\Pattern\Meta\Mock;

use Commons\Pattern\Plugin\Impl\Standard;

class SuperPowerPlugin extends Standard
{
    public $superPower = 'Super ';

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::preDispatch()
     */
    public function preDispatch(\Commons\Pattern\Plugin\Context $context)
    {
        $this->superPower .= 'pre ';
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::postDispatch()
     */
    public function postDispatch(\Commons\Pattern\Plugin\Context $context)
    {
        $this->superPower .= $context->getReturn(). ' post ';
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::onErrorDispatch()
     */
    public function errorDispatch(\Commons\Pattern\Plugin\Context $context)
    {
        $this->superPower .= $context->getException()->getMessage(). ' error ';
        $context->rethrowException();
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::finallyDispatch()
     */
    public function finallyDispatch(\Commons\Pattern\Plugin\Context $context)
    {
        $this->superPower .= 'final Spell.';
    }
}
