<?php

namespace CommonsTest\Pattern\Plugin\Mock;

use Commons\Pattern\Plugin\Pluggable;
use Commons\Pattern\Plugin\Dispatcher;
use CommonsTest\Pattern\Plugin\Mock\CacheMock;

class PluggableClass implements Pluggable
{
    use \Commons\Pattern\Plugin\TPluggableHandler;

    public $verifier = 0;

    protected function registerPlugins(Dispatcher $dispatcher)
    {
        $cache = new CacheMock('/\bcacheFunction\b/');
        $dispatcher->addPlugin('cache', $cache);
    }

    public function cacheFunction($paramRef)
    {
        ++$this->verifier;
        $paramRef = $paramRef. ' alterado ';
        return 'Result '. $paramRef;
    }
}
