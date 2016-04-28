<?php

namespace CommonsTest\Util\Test;

class ServiceSample
{

    public function __construct()
    {}

    public function sum($a, $b)
    {
        return $a + $b;
    }

    public function error()
    {
        throw new \Exception("error");
    }

    public function complexResult()
    {
        $cls = new \stdClass();
        $cls->test = 'This is a complex test.';
        return $cls;
    }

    public function nonExpectedResult($a, $b)
    {
        throw new \Exception("Unexpected error for failure.");
    }

    private function privateSum($a, $b)
    {
        return $a + $b;
    }
}
