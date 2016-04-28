<?php

namespace Commons\Pattern\Transaction\Strategy;

use Commons\Pattern\Delegate\Delegate;
use Commons\Pattern\Plugin\Pluggable;
use Commons\Pattern\Transaction\Transactional;

/**
 * Interface que define a estratégia transacional.
 */
interface TransactionStrategy extends Delegate, Transactional
{
    /**
     * Nome da estratégia transacional.
     */
    public function getName();

    /**
     * @param \Commons\Pattern\Plugin\Pluggable $feature
     */
    public function setFeature(Pluggable $feature);

    /**
     * @return \Commons\Pattern\Plugin\Pluggable
     */
    public function getFeature();
}
