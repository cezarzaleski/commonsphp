<?php

namespace Commons\Pattern\Plugin\Impl;

use Commons\Pattern\Plugin\Plugin;
use Commons\Pattern\Plugin\Context;

/**
 *
 * @category Commons
 * @package Commons\Pattern\Plugin\Impl
 */
abstract class Selector extends Standard implements Plugin
{

    /**
     * Representa um Regex com a string de pesquisa.
     *
     * @var string
     */
    protected $regex = '';

    /**
     * Construtor padrão.
     *
     * @param array $regex String com o regex que seleciona um método.
     */
    public function __construct($regex)
    {
        $this->regex = $regex;
    }

    /**
     * Verifica se o contexto é válido para processamento.
     *
     * @param Context $context
     * @return boolean
     */
    public function isValidContext(Context $context)
    {
        // verifica se operação deve ser verificada
        if (! \preg_match($this->regex, $context->getOperation())) {
            return false;
        }

        return true;
    }
}
