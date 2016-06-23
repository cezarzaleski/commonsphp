<?php

namespace Commons\Pattern\Transaction\Strategy;

use Commons\Pattern\Plugin\Pluggable;
use Commons\Pattern\Identifier\TNameable;

/**
 * Estratégia para utilização de transação dos adaptadores.
 */
abstract class StandardTransactionStrategy implements TransactionStrategy
{
    // A estratégica transacional é um objeto do qual se pode obter seu nome.
    use TNameable {
        setName as protected;
    }

    /**
     * @var \Commons\Pattern\Plugin\Pluggable
     */
    private $feature;

    /**
     * Construtor padrão.
     *
     * @param string $name
     * @param \Commons\Pattern\Plugin\Pluggable $feature
     */
    public function __construct($name = null, $feature = null)
    {
        $this->setName($name);
        $this->feature = $feature;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Strategy\TransactionStrategy::setFeature()
     */
    public function setFeature(Pluggable $feature)
    {
        $this->feature = $feature;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Strategy\TransactionStrategy::getFeature()
     */
    public function getFeature()
    {
        return $this->feature;
    }
}
