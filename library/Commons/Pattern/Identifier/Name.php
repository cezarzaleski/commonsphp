<?php
namespace Commons\Pattern\Identifier;

/**
 * Interface de marcação que representa a capacidade de um objeto dizer qual seu próprio nome.
 */
interface Name
{
    /**
     * Retorna o nome do objeto.
     *
     *  @return string
     */
    public function getName();
}
