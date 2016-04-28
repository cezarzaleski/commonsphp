<?php
namespace Commons\Pattern\Transaction\Strategy;

use Commons\Pattern\Plugin\Pluggable;

/**
 * Estratégia para utilização de transação dos adaptadores.
 */
abstract class StandardTransactionStrategy implements TransactionStrategy
{

    /**
     * @var string
     */
    private $name;

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
        $this->name = $name;
        $this->feature = $feature;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Transaction\Strategy\TransactionStrategy::getName()
     */
    public function getName()
    {
        return $this->name;
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
