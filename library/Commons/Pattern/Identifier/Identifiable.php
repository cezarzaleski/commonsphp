<?php

namespace Commons\Pattern\Identifier;

/**
 *  Representa uma interface comum para identificação.
 *
 *  Não representa identificador único de objetos.
 *  É um mecanismo de chave substituta.
 */
interface Identifiable
{
    /**
     * Recupera o identificador.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Insere o identificador.
     *
     * @param mixed $id
     */
    public function setId($id);
}
