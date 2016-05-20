<?php
namespace CommonsTest\Pattern\Meta\Mock;

use Commons\Pattern\Plugin\Impl\PluggableDecorator;

class SuperPowerPluggable extends PluggableDecorator
{
    public function __construct()
    {
        parent::__construct(null, array('superPower' => new SuperPowerPlugin()));
    }

    public function resetSuperPower()
    {
        $this->getPluginDispatcher()->getPlugin('superPower')->superPower = 'Super ';
    }

    public function getSuperPower()
    {
        return $this->getPluginDispatcher()->getPlugin('superPower')->superPower;
    }
}
