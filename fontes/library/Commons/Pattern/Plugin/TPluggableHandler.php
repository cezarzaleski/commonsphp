<?php

namespace Commons\Pattern\Plugin;

use Commons\Pattern\Plugin\Dispatcher;

/**
 * Trait que define mecanismos básicos para tornar uma classe plugável.
 *
 * @see \Commons\Pattern\Plugin\Pluggable
 */
trait TPluggableHandler
{
    /**
     * Dispachante para execução de plugins definidos para esta classe.
     *
     * @var \Common\Pattern\Plugin\Dispatcher
     */
    protected $dispatcher;

    /**
     * Define um Despachante com plugins de interceptação.
     *
     * @return \Common\Pattern\Plugin\Dispatcher
     */
    public function getPluginDispatcher()
    {
        if (! $this->dispatcher) {
            $dispatcher = new Dispatcher();
            $this->registerPlugins($dispatcher);
            $this->dispatcher = $dispatcher;
        }

        return $this->dispatcher;
    }

    /**
     * Registrar os plugins no Despachante.
     *
     * @param \Common\Pattern\Plugin\Dispatcher $dispatcher
     * @return void
     */
    abstract protected function registerPlugins(Dispatcher $dispatcher);
}
