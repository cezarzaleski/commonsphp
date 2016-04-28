<?php

namespace CommonsTest\Pattern\Service;

use Zend\Di\ServiceLocator;
use Commons\Pattern\Service\Impl\ZendServiceLookupManager;
use Zend\Log\PsrLoggerAdapter;
use Zend\Log\Logger;
use Zend\Log\Writer\Noop;

class CoreServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExemploService
     */
    private static $service;

    public function testService()
    {
        $locator = new ServiceLocator();
        $logger = new Logger();
        $logger->addWriter(new Noop());
        self::$service = new ExemploService(new ZendServiceLookupManager($locator), new PsrLoggerAdapter($logger));
        $locator->set('helloService', self::$service);

        self::assertEquals('Hello World.', self::$service->operacaoHelloWorld());
    }

    public function testCompositeService()
    {
        self::assertEquals('Hello World.', self::$service->operacaoHelloWorldFromOtherService());
    }

}
