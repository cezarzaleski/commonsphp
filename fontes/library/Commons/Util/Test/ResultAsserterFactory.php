<?php
namespace Commons\Util\Test;

abstract class ResultAsserterFactory
{
    public static function create($callable)
    {
        return new GenericResultAsserter($callable);
    }
}
