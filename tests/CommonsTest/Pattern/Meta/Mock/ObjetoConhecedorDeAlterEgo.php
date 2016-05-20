<?php

namespace CommonsTest\Pattern\Meta\Mock;

use Commons\Pattern\Meta\TAlterEgoAware;
use Commons\Pattern\Meta\SelfAwareness;

class ObjetoConhecedorDeAlterEgo implements SelfAwareness
{
    use TAlterEgoAware;

    /**
     * @return number
     */
    public function getForca()
    {
        return 10;
    }

    public function descreverForca()
    {
        return "Possuo ".$this->getForca()." de força, meu alter ego possui ".$this->alterThis()->getForca()." de força.";
    }

    /**
     * @return \CommonsTest\Pattern\Meta\Mock\ObjetoConhecedorDeAlterEgo
     */
    public function getAlter() {
        return $this->alterThis();
    }
}
