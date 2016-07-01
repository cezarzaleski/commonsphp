<?php
namespace Commons\Pattern\Validator\Impl;

use Commons\Pattern\Validator\Validatable;
use Symfony\Component\Validator\Validation;

/**
 * Validador que avalia anotações no objeto.
 *
 * Obs: Como verifica anotações então depende do Doctrine.
 *
 * Referência para anotações comuns do Symfony:
 * http://symfony.com/doc/current/reference/constraints.html
 *
 * Referência para criação de anotações customizadas (user-defined, custom):
 * http://symfony.com/doc/current/cookbook/validation/custom_constraint.html
 */
class AnnotationValidator implements Validatable
{
    /**
     * Violaçoes em caso de falha.
     *
     * @var array
     */
    private $violations = array();

    /**
     * {@inheritDoc}
     * @see \Zend\Validator\ValidatorInterface::isValid()
     */
    public function isValid($value)
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $this->violations = $validator->validate($value);
        return $this->violations->count() == 0;
    }

    /**
     * {@inheritDoc}
     * @see \Zend\Validator\ValidatorInterface::getMessages()
     */
    public function getMessages()
    {
        return $this->violations;
    }
}
