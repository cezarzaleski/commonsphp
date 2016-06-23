<?php

namespace Commons\Pattern\Identifier;

/**
 * Interface de marcação que representa a capacidade de um objeto dizer qual sua própria identificação.
 */
interface Identification
{
    /**
     * Recupera o identificador.
     *
     * @return mixed
     */
    public function getId();
}
