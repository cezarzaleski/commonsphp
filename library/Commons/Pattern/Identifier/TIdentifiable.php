<?php

namespace Commons\Pattern\Identifier;

/**
 *  Trait com recursos mínimos para identificação.
 */
trait TIdentifiable
{
    /**
     * Identificador do objeto.
     *
     * @var mixed
     */
    protected $id;

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Identifier\Identifiable::getId()
     */
    public function getId()
    {
        return $this->id;
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Identifier\Identifiable::setId()
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
