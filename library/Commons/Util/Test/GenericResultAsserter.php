<?php

namespace Commons\Util\Test;

use Commons\Exception\InvalidArgumentException;

class GenericResultAsserter implements ResultAsserter
{

    private $callable;

    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function assertResult($testCase, $object, $result)
    {
        $call = $this->callable;
        if (!$call) {
            throw new InvalidArgumentException('Undefined callback for ResultAsserter.');
        }
        return $call($testCase, $object, $result);
    }
}
