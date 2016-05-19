<?php

namespace Commons\Pattern\Plugin\Impl;

use Commons\Pattern\Plugin\Pluggable;
use Commons\Pattern\Plugin\TPluggableHandler;
use Commons\Pattern\Plugin\Dispatcher;

/**
 * Decorador de repositórios de Plugins.
 */
class PluggableDecorator implements Pluggable
{
    use TPluggableHandler {
        getPluginDispatcher as private getTraitPluginDispatcher;
    }

    /**
     * @var Pluggable
     */
    private $pluggable;

    /**
     * @var array
     */
    private $plugins;

    /**
     * Construtor padrão.
     */
    public function __construct(Pluggable $pluggable = null, array $plugins = array())
    {
        $this->pluggable = $pluggable;
        $this->plugins = $plugins;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Pluggable::getPluginDispatcher()
     */
    public function getPluginDispatcher()
    {
        if ($this->pluggable) {
            if (! $this->dispatcher) {
                $this->dispatcher = $this->pluggable->getPluginDispatcher();
                $this->registerPlugins($this->dispatcher);
            }
        } else {
            $this->dispatcher = $this->getTraitPluginDispatcher();
        }
        return $this->dispatcher;
    }

    /**
     * @param Dispatcher $dispatcher
     */
    protected function registerPlugins(Dispatcher $dispatcher)
    {
        if (!empty($this->plugins)) {
            foreach ($this->plugins as $name => $plugin) {
                $dispatcher->addPlugin($name, $plugin);
            }
        }
    }
}
