<?php

namespace CommonsTest\Pattern\Service;

use Commons\Pattern\Service\Impl\ZendServiceLookupManager;
use Zend\Di\ServiceLocator;
use Zend\Log\PsrLoggerAdapter;
use Zend\Log\Logger;

class ZendServiceLookupManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ZendServiceLookupManager
     */
    private static $lookup;

    public function testCreateLookupManager()
    {
        try {
            $locator = new ServiceLocator();
            self::$lookup = new ZendServiceLookupManager($locator);

            $locator->set('test', new ExemploService(self::$lookup, new PsrLoggerAdapter(new Logger())));
            $locator->set('notAService', 'string');

            self::assertTrue(self::$lookup->get('test') != null);
        } catch(\Exception $e) {
            self::fail($e->getMessage());
        }
    }

    public function testLookupManagerGet()
    {
        self::assertTrue(self::$lookup->get('test') != null);
        self::assertTrue(self::$lookup->get('test') instanceof ExemploService);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Instância do serviço notAService não é do tipo \Commons\Pattern\Service\Service.
     */
    public function testLookupManagerGetExceptionOnNotAService()
    {
        self::$lookup->get('notAService');
    }
}
