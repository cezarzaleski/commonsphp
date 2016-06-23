<?php

namespace Commons\Pattern\Identifier;

/**
 * Interface de marcação que representa a capacidade do objeto de ser nomeável.
 */
interface Nameability
{
    /**
     * Insere o nome do objeto.
     *
     * @param string $name
     */
    public function setName($name);
}
