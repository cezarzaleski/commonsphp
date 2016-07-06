<?php

namespace Commons\Pattern\Meta;

/**
 * Objeto consciente de que existe um proxy dele mesmo.
 */
trait TAlterEgoAware
{

    /**
     * @var mixed
     */
    private $alterThis = null;

    /**
     * Método responsável por retornar a instância do proxy ou o próprio objeto.
     *
     * @return mixed retorna o Proxy do objeto ou o próprio objeto.
     */
    protected function alterThis()
    {
        return ($this->alterThis) ?: $this;
    }
}
