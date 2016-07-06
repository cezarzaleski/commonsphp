<?php

namespace CommonsTest\Pattern\Service;

use Zend\Di\ServiceLocator;
use Commons\Pattern\Service\Impl\ZendServiceLookupManager;
use Zend\Log\Logger;
use Zend\Log\Writer\Noop;
use Commons\Pattern\Log\DefaultZendPsrLoggerAdapter;

class CoreServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExemploService
     */
    private static $service;

    public static function setUpBeforeClass()
    {
        $locator = new ServiceLocator();
        $logger = new Logger();
        $logger->addWriter(new Noop());
        self::$service = new ExemploService(
            new ZendServiceLookupManager($locator),
            new DefaultZendPsrLoggerAdapter($logger)
            );
        $locator->set('helloService', self::$service);
    }

    public function testService()
    {
        self::assertEquals('Hello World.', self::$service->operacaoHelloWorld());
    }

    public function testCompositeService()
    {
        self::assertEquals('Hello World.', self::$service->operacaoHelloWorldFromOtherService());
    }

}
