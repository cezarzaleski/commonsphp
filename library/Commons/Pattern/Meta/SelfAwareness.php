<?php

namespace Commons\Pattern\Meta;

/**
 * Auto-conhecimento.
 */
interface SelfAwareness
{
    /**
     * Retorna a instância do alterego do objeto ou o próprio objeto.
     * Função de atribuir qualitativamente o uso de interfaces associadas ao objeto
     * ou recuperação de proxies, delegadores ou interceptadores do objeto.
     *
     * @return mixed o próprio objeto ou seu proxy.
     */
    public function getAlter();
}
