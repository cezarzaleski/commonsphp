<?php

namespace Commons\Util\Test;

use Commons\Exception\InvalidArgumentException;

class GenericResultAsserter implements ResultAsserter
{

    private $callableAsserter;

    public function __construct($callable)
    {
        $this->callableAsserter = $callable;
    }

    public function assertResult($testCase, $object, $result)
    {
        $call = $this->callableAsserter;
        if (!$call) {
            throw new InvalidArgumentException('Undefined callback for ResultAsserter.');
        }
        return $call($testCase, $object, $result);
    }
}
