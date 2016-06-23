<?php

namespace Commons\Pattern\Identifier;

/**
 *  Trait com recursos mínimos para nomeação.
 */
trait TNameable
{
    /**
     * Nome do objeto.
     *
     * @var mixed
     */
    protected $name;

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Identifier\Nameable::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /* (non-PHPdoc)
     * @see \Commons\Pattern\Identifier\Nameable::setName()
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
