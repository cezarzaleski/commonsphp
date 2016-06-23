<?php

namespace Commons\Pattern\Repository\Impl;

use Commons\Pattern\Identifier\Identifiable;
use Commons\Pattern\Identifier\TIdentifiable;
use Commons\Pattern\Identifier\Name;
use Commons\Pattern\Identifier\TNameable;

/**
 * Representa uma referência de um objeto.
 */
class Reference implements Identifiable, Name
{
    // A referência é um objeto identificável
    use TIdentifiable;

    // A referência é um objeto do qual se pode obter seu nome.
    use TNameable {
        setName as protected;
    }

    /**
     * Referência para o objeto será parcial.
     *
     * @var integer
     */
    const PARTIAL = 0;

    /**
     * Referência para o objeto será completa.
     *
     * @var integer
     */
    const FULL = 1;

    /**
     * Representa o tipo da referência (PARTIAL ou FULL).
     *
     * @var number
     */
    private $type;

    /**
     * Construtor padrão.
     *
     * @param string $name Nome completo da entidade que se quer referenciar.
     * @param array $keys Array de chaves do tipo array('codigoTipo1' => 1, 'coditoTipo2' => 2)
     * @param number $type pode ser do tipo Reference::PARTIAL (default) ou Reference::FULL
     */
    public function __construct($name, array $keys, $type = Reference::PARTIAL)
    {
        $this->setName($name);
        $this->setId($keys);
        $this->type = $type;
    }

    /**
     * Recupera o tipo da referência (parcial ou completa)
     *
     * @return number
     */
    public function getType()
    {
        return $this->type;
    }
}
