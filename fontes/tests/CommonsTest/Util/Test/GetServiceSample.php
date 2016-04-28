<?php

namespace CommonsTest\Util\Test;

class GetServiceSample
{

    public static function getInstance()
    {
        return new GetServiceSample();
    }

    public function sum($a, $b)
    {
        return $a + $b;
    }
}
