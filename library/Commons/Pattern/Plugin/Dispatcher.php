<?php

namespace Commons\Pattern\Plugin;

/**
 * Representa um concentrador dos plugins.
 *
 * @category Commons
 * @package Commons\Pattern\Plugin
 */
final class Dispatcher implements Plugin
{

    /**
     * Array que contém a lista de plugins utilizados pelo dispatcher.
     *
     * @var \Commons\Pattern\Plugin\Plugin[]
     */
    private $plugins = array();

    /**
     * Adiciona um plugin à pilha de plugins.
     *
     * @param string $name
     * @param \Commons\Pattern\Plugin\Plugin $plugin
     * @return \Commons\Pattern\Plugin\Dispatcher Provê interface fluente.
     */
    public function addPlugin($name, Plugin $plugin)
    {
        $this->plugins[$name] = $plugin;
        $plugin->setDispatcher($this);
        return $this;
    }

    /**
     * Verifica se um plugin existe.
     *
     * @param string $name
     */
    public function hasPlugin($name)
    {
        return isset($this->plugins[$name]);
    }

    /**
     *
     * @param string $name
     * @return \Commons\Pattern\Plugin\Plugin
     */
    public function getPlugin($name)
    {
        return $this->plugins[$name];
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Plugin\Plugin::preDispatch()
     */
    public function preDispatch(Context $context)
    {
        foreach ($this->plugins as $plugin) {
            if ($context->isLocked()) {
                break;
            }
            $plugin->preDispatch($context);
        }
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Plugin\Plugin::postDispatch()
     */
    public function postDispatch(Context $context)
    {
        $this->postponeDispatch($context, 'postDispatch');
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::errorDispatch()
     */
    public function errorDispatch(Context $context)
    {
        $this->postponeDispatch($context, 'errorDispatch');
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::finallyDispatch()
     */
    public function finallyDispatch(Context $context)
    {
        $this->postponeDispatch($context, 'finallyDispatch');
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Plugin\Plugin::setDispatcher()
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        throw new \BadMethodCallException('Unsupported operation exception. This class is itself a dispatcher.');
    }

    /**
     * Realiza a chamada das ações do tipo $action posteriores a execução do método envolvido.
     * @param Context $context
     * @param string $action
     */
    private function postponeDispatch(Context $context, $action)
    {
        foreach (\array_reverse($this->plugins) as $plugin) {
            if ($context->isLocked()) {
                break;
            }
            $plugin->$action($context);
        }
    }
}
