<?php

namespace CommonsTest\Pattern\Plugin\Mock;

use Commons\Pattern\Plugin\Pluggable;
use Commons\Pattern\Plugin\Dispatcher;
use Commons\Pattern\Plugin\Impl\StandardCachePlugin;

class InvalidCachePluggableClass implements Pluggable
{
    public $verifier = 0;

    private $dispatcher;

	/* (non-PHPdoc)
     * @see \Commons\Pattern\Plugin\Pluggable::getPluginDispatcher()
     */
    public function getPluginDispatcher()
    {
        if (! $this->dispatcher) {
            $dispatcher = new Dispatcher();
            $cache = new StandardCachePlugin('', array());
            $dispatcher->addPlugin('cache', $cache);
            $this->dispatcher = $dispatcher;
        }

        return $this->dispatcher;
    }
}
