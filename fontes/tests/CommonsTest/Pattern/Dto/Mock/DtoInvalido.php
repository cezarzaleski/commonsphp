<?php

namespace Commons\Pattern\Dto;

/**
 * Representa um Dto Invalido.
 */
class DtoInvalido
{

    protected $id;

    /**
     *
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
