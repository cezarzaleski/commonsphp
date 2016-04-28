<?php

namespace Commons\Pattern\Enum;

use Commons\Exception\InvalidArgumentException;

/**
 * Classe base para enumerações.
 */
class Enum
{

    /**
     * Constante default.
     *
     * @var const
     */
    const DEFAULT_VALUE = null;

    /**
     * Valor atual da instância.
     *
     * @var mixed
     */
    protected $constValue;

    /**
     * Construtor padrão.
     *
     * @param mixed $value
     * @throws
     *
     *
     */
    public function __construct($value = null)
    {
        $ref = new \ReflectionClass($this);
        if ($value) {
            $list = $ref->getConstants();
            if (in_array($value, $list)) {
                $this->constValue = $value;
            } else {
                $class = get_class($this);
                throw new InvalidArgumentException(
                    vsprintf(
                        'Valor %s inválido para enumeração %s.',
                        array($value,$class)
                    )
                );
            }
        } else {
            $this->constValue = $ref->getConstant('DEFAULT_VALUE');
        }
    }

    /**
     * Descobre o nome da constante a partir de seu valor.
     *
     * @param mixed $arg
     * @return string
     */
    final public static function valueOf($arg)
    {
        return array_search($arg, static::extractConsts());
    }

    /**
     * Verifica se um valor de constante é válido.
     *
     * @param mixed $arg
     * @return boolean
     */
    final public static function isValid($arg)
    {
        return in_array($arg, static::extractConsts(), true);
    }

    /**
     * Retorna todas as constantes do Enum.
     *
     * @return array
     */
    final public static function values()
    {
        return static::extractConsts();
    }

    /**
     * Extrai todas as constantes de uma enumeração.
     *
     * @return array
     */
    final private static function extractConsts()
    {
        $calledClass = get_called_class();
        $ref = new \ReflectionClass($calledClass);
        $list = $ref->getConstants();
        unset($list['DEFAULT_VALUE']);
        return $list;
    }

    /**
     * Representa a classe de enumeração pelo valor da constante
     * que ela contém.
     *
     * @return string
     */
    final public function __toString()
    {
        return (string) $this->constValue;
    }
}
