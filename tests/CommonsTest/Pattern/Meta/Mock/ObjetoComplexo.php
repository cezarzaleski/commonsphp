<?php

namespace CommonsTest\Pattern\Meta\Mock;

class ObjetoComplexo
{
    public $propriedadePublica = 'string publica';

    private $parametro;

    public function __construct($parametro)
    {
        $this->parametro = $parametro;
    }

    public function recuperarParametro($param1, $param2)
    {
        return $this->parametro . ' ' . $param1 . ' ' . $param2;
    }

    public function lancaExcecao()
    {
        throw new \LogicException('Teste erro');
    }

    public static function sayHello()
    {
        return 'Hello!';
    }

    public function __invoke()
    {
        return 'Invoker';
    }

    public function __toString()
    {
        return 'Objeto Complexo';
    }
}
