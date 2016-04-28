<?php
namespace Commons\Util\Test;

interface ResultAsserter
{
    public function assertResult($testCase, $object, $result);
}
