<?php

namespace Commons\Util\Input;

use Zend\InputFilter\InputFilter as ZendInputFilter;
use Zend\InputFilter\Input as ZendInput;

/**
 * Classe base para tratamento das validações de entrada.
 *
 * @category Commons
 * @package Commons\Util\Input
 */
class Input extends ZendInputFilter
{

    /**
     * Responsável por adicionar Validadores para um campo que se deseja validar.
     *
     * @param string $fieldName
     *            Nome do campo.
     * @param mixed $field
     *            Dados que se deseja testar.
     * @param array $validators
     *            Array de validadores.
     * @param string $required
     *            Se o campo é obrigatório ou não.
     * @param string $allowEmpty
     *            Se permite campo vazio.
     * @param array $filters
     *            Array de nomes de filtros ou Array de array[2] com nome e opções de filtro.
     * @return \Commons\Util\Input\Input Retorna uma interface fluente.
     */
    public function addValidator(
        $fieldName,
        $field,
        $validators,
        $required = false,
        $allowEmpty = false,
        $filters = array()
    ) {
        $input = new ZendInput($fieldName);
        if (! is_array($validators)) {
            $validators = array(
                $validators
            );
        }
        foreach ($validators as $val) {
            $input->getValidatorChain()->addValidator($val, false);
        }
        $input->setRequired($required);
        $input->setAllowEmpty($allowEmpty);
        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                $input->getFilterChain()->attach($filter);
            } else {
                $options = array();
                if (is_array($filter)) {
                    // tem que ser um array
                    $options = $filter[1];
                    // tem que ser o nome do filtro
                    $filter = $filter[0];
                }
                $input->getFilterChain()->attachByName($filter, $options);
            }
        }
        $input->setValue($field);
        $currentData = ($this->data) ? $this->data : array();
        $this->setData(
            array_merge(
                $currentData,
                array($fieldName => $field)
            )
        );
        $this->add($input, $fieldName);
        return $this;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Zend\InputFilter\Input::process()
     */
    public function process()
    {
        if (! $this->isValid()) {
            throw new InputException('Entrada inválida.', null, null, $this);
        }

        return $this;
    }
}
