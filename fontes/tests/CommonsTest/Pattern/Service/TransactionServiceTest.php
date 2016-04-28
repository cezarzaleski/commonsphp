<?php
namespace CommonsTest\Pattern\Service;

use Commons\Pattern\Service\Impl\TransactionalService;
use Zend\Di\ServiceLocator;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Commons\Pattern\Service\Impl\ZendServiceLookupManager;
use Commons\Pattern\Transaction\Transaction;
use Zend\Log\PsrLoggerAdapter;

class TransactionServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TransactionalService
     */
    private static $service;

    public function testCreateTransactionService()
    {
        $locator = new ServiceLocator();
        $logger = new Logger();
        $logger->addWriter(new Stream('php://output'));

        self::$service = new TransactionalService(
            new DummyTransactionStrategy('dummy'),
            new ZendServiceLookupManager($locator),
            new PsrLoggerAdapter($logger));

        // verifica se a estratégia está realmente registrada no contexto de transações.
        self::assertTrue(Transaction::isRegistered('dummy'));
    }

    public function testBeginTransaction()
    {
        Transaction::getStrategy('dummy')->cleanState();
        self::assertEquals(null, Transaction::getStrategy('dummy')->getState());
        self::$service->beginTransaction();
        self::assertEquals('beginTransaction', Transaction::getStrategy('dummy')->getState());
        self::$service->commit();
    }

    public function testClose()
    {
        Transaction::getStrategy('dummy')->cleanState();
        self::assertEquals(null, Transaction::getStrategy('dummy')->getState());
        self::$service->close();
        self::assertEquals('close', Transaction::getStrategy('dummy')->getState());
    }

    public function testCommit()
    {
        Transaction::getStrategy('dummy')->cleanState();
        self::assertEquals(null, Transaction::getStrategy('dummy')->getState());
        self::$service->beginTransaction();
        self::assertEquals('beginTransaction', Transaction::getStrategy('dummy')->getState());
        self::$service->commit();
        self::assertEquals('commit', Transaction::getStrategy('dummy')->getState());
    }

    public function testRollback()
    {
        Transaction::getStrategy('dummy')->cleanState();
        self::assertEquals(null, Transaction::getStrategy('dummy')->getState());
        self::$service->beginTransaction();
        self::assertEquals('beginTransaction', Transaction::getStrategy('dummy')->getState());
        self::$service->rollback();
        self::assertEquals('rollback', Transaction::getStrategy('dummy')->getState());
    }

    public function testDemarcate()
    {
        Transaction::getStrategy('dummy')->cleanState();
        self::assertEquals(null, Transaction::getStrategy('dummy')->getState());
        self::$service->demarcate(function(){});
        self::assertEquals('commit', Transaction::getStrategy('dummy')->getState());
    }
}
