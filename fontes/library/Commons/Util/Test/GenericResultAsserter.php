<?php
namespace Commons\Util\Test;

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
        return $call($testCase, $object, $result);
    }
}
