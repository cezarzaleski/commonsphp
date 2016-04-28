<?php

namespace Commons\Util\Input;

use Commons\Exception\InvalidArgumentException;
use Commons\Exception\CommonsException;

/**
 * Indica que parâmetro de entrada é inválido.
 *
 * @category Commons
 * @package Commons\Exception
 */
class InputException extends InvalidArgumentException implements CommonsException
{

    /**
     *
     * @var Input
     */
    protected $input = array();

    /**
     *
     * @param string $message
     * @param integer $code
     * @param Exception $previous
     * @param \Commons\Util\Input\Input $input
     */
    public function __construct($message, $code, $previous, Input $input)
    {
        $this->input = $input;
        parent::__construct($message, $code, $previous);
    }

    /**
     *
     * @return \Commons\Util\Input\Input
     */
    public function getInput()
    {
        return $this->input;
    }
}
