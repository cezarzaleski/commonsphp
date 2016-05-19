<?php

namespace CommonsTest\Util\Test;

use Commons\Util\Test\GenericTestCase;
use Commons\Util\Test\ResultAsserterFactory;

class GenericTestCaseTest extends GenericTestCase
{

    /**
     *
     * @param string $class
     * @param string $method
     * @param mixed $expectedResult
     * @param array $params
     *            @dataProvider publicInterface
     */
    public function testPublicInterface($class, $method, $expectedResult, $params)
    {
        $this->assertPublicInterface($class, $method, $expectedResult, $params);
    }

    public function publicInterface()
    {
        return array(
            array(
                'CommonsTest\Util\Test\ServiceSample',
                '__construct',
                null,
                array()
            ),
            array(
                'CommonsTest\Util\Test\ServiceSample',
                'sum',
                3,
                array(
                    1,
                    2
                )
            ),
            array(
                'CommonsTest\Util\Test\ServiceSample',
                'error',
                new \Exception("error"),
                null
            ),
            array(
                'CommonsTest\Util\Test\ServiceSample',
                'complexResult',
                ResultAsserterFactory::create(function ($testCase, $object, $result)
                {
                    $testCase->assertEquals('This is a complex test.', $result->test);
                }),
                null
            )
        );
    }

    /**
     * @expectedException \PHPUnit_Framework_AssertionFailedError
     * @expectedExceptionMessage Unexpected error for failure.
     */
    public function testUnexpectedResult()
    {
        $this->assertPublicInterface('CommonsTest\Util\Test\ServiceSample', 'nonExpectedResult', 3, array(
            1,
            2
        ));
    }

    public function testClassNotExists()
    {
        $this->assertPublicInterface(null, null, new \ReflectionException("Class  does not exist"), null);
    }

    /**
     */
    public function testClassNotInstancePublicConstructor()
    {
        $this->assertPublicInterface('CommonsTest\Util\Test\ServiceSample', 'sum', 3, array(
            1,
            2
        ));
    }

    /**
     */
    public function testClassNotInstanceGetInstanceMethod()
    {
        $this->assertPublicInterface('CommonsTest\Util\Test\GetServiceSample', 'sum', 3, array(
            1,
            2
        ));
    }

    /**
     */
    public function testClassInstance()
    {
        $this->assertPublicInterface(new ServiceSample(), 'sum', 3, array(
            1,
            2
        ));
    }

    public function testClassInstanceMethodNotExists()
    {
        $this->assertPublicInterface(new ServiceSample(), 'sumThatDontExists', new \ReflectionException("Method sumThatDontExists does not exist"), array(
            1,
            2
        ));
    }

    public function testClassInstanceMethodNotPublic()
    {
        $this->assertPublicInterface(new ServiceSample(), 'privateSum', new \ReflectionException("O método privateSum da classe CommonsTest\Util\Test\ServiceSample não é público."), array(
            1,
            2
        ));
    }
}
