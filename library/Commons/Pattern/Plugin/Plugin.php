<?php

namespace Commons\Pattern\Plugin;

/**
 * Interface que define o contrato de Plugins.
 *
 * @category Commons
 * @package Commons\Pattern\Plugin
 */
interface Plugin
{

    /**
     * Método que realiza operações anteriores à execução de um procedimento.
     *
     * @param \Commons\Pattern\Plugin\Context $context
     */
    public function preDispatch(Context $context);

    /**
     * Método que realiza operações posteriores à execução de um procedimento.
     *
     * @param \Commons\Pattern\Plugin\Context $context
     */
    public function postDispatch(Context $context);

    /**
     * Método que guarda a referência para o dispatcher.
     *
     * @param \Commons\Pattern\Plugin\Dispatcher $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher);
}
