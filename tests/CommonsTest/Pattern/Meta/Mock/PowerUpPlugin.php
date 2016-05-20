<?php

namespace CommonsTest\Pattern\Meta\Mock;

use Commons\Pattern\Plugin\Impl\Standard;
use Commons\Pattern\Plugin\Context;

class PowerUpPlugin extends Standard
{
    private $powerUp = 1;

    public function __construct($power)
    {
        $this->powerUp = $power;
    }

    public function preDispatch(Context $context)
    {
    }

    public function postDispatch(Context $context)
    {
        $context->setReturn($context->getReturn()*$this->powerUp);
    }
}
