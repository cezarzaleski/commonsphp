<?php

namespace Commons\Pattern\Identifier;

/**
 * Interface de marcação que representa a capacidade do objeto de ser identificável.
 */
interface Identifiability
{
    /**
     * Insere o identificador.
     *
     * @param mixed $id
     */
    public function setId($id);
}
