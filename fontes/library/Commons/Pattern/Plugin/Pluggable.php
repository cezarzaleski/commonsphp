<?php

namespace Commons\Pattern\Plugin;

/**
 * Demarca as classes que se utilizam de plugins.
 *
 * @category Commons
 * @package Commons\Pattern\Plugin
 */
interface Pluggable
{

    /**
     * Responsável por expor o dispatcher de plugins.
     *
     * @return \Commons\Pattern\Plugin\Dispatcher
     */
    public function getPluginDispatcher();
}
