<?php

namespace Commons\Pattern\Delegate;

interface Delegate
{

    /**
     * Retorna o objeto delegador.
     *
     * @return mixed
     */
    public function getAssigner();
}
