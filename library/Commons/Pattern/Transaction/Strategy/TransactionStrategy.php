<?php

namespace Commons\Pattern\Transaction\Strategy;

use Commons\Pattern\Delegate\Delegate;
use Commons\Pattern\Plugin\Pluggable;
use Commons\Pattern\Transaction\Transactional;
use Commons\Pattern\Identifier\Name;

/**
 * Interface que define a estratégia transacional.
 */
interface TransactionStrategy extends Delegate, Transactional, Name
{
    /**
     * @param \Commons\Pattern\Plugin\Pluggable $feature
     */
    public function setFeature(Pluggable $feature);

    /**
     * @return \Commons\Pattern\Plugin\Pluggable
     */
    public function getFeature();
}
