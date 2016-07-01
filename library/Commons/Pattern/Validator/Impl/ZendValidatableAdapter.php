<?php

namespace Commons\Pattern\Validator\Impl;

use Zend\Validator\ValidatorInterface;
use Commons\Pattern\Validator\Validatable;

/**
 * Adaptador Validatable para interfaces ValidatorInterface.
 */
class ZendValidatableAdapter implements ValidatorInterface
{

    /**
     * @var Validatable
     */
    private $validatable;

    /**
     * Construtor padrão.
     *
     * @param Validatable $validatable
     */
    public function __construct(Validatable $validatable)
    {
        $this->validatable = $validatable;
    }

    /**
     * {@inheritDoc}
     * @see \Zend\Validator\ValidatorInterface::isValid()
     */
    public function isValid($value)
    {
        return $this->validatable->isValid($value);
    }

    /**
     * {@inheritDoc}
     * @see \Zend\Validator\ValidatorInterface::getMessages()
     */
    public function getMessages()
    {
        return $this->validatable->getMessages();
    }
}
