<?php
namespace CommonsTest\Pattern\Transaction;

use Commons\Pattern\Transaction\Transaction;
use Commons\Pattern\Transaction\Strategy\LoggerTransactionStrategy;
use Zend\Log\Logger;
use Zend\Log\Writer\Mock;
use Zend\Log\PsrLoggerAdapter;
use CommonsTest\Pattern\Transaction\Mock\TransactionalMethod;
use Commons\Pattern\Meta\MetaPluggablesBuilder;
use Commons\Pattern\Transaction\Plugin\Transactional;
use Commons\Pattern\Meta\MetaObject;
use Commons\Pattern\Plugin\Impl\PluggableDecorator;

class TransactionAnnotationTest extends \PHPUnit_Framework_TestCase
{
    private static $logger = null;

    public static function setUpBeforeClass()
    {
        Transaction::initialize();

        self::$logger = new Logger();
        self::$logger->addWriter(new Mock());

        Transaction::registerStrategy(new LoggerTransactionStrategy(new PsrLoggerAdapter(self::$logger), 'logger'));
        Transaction::registerStrategy(new LoggerTransactionStrategy(new PsrLoggerAdapter(self::$logger), 'logger2'));
        Transaction::registerStrategy(new LoggerTransactionStrategy(new PsrLoggerAdapter(self::$logger), 'compositeLogger'));
    }

    /**
     * Responsável por criar um proxy do objeto para entender a transação.
     *
     * @return \CommonsTest\Pattern\Transaction\Mock\TransactionalMethod
     */
    private function getTransactionalMethod()
    {
        $service = new TransactionalMethod();

        $pluggables = new MetaPluggablesBuilder();
        $pluggables->setCallPluggable(new PluggableDecorator(null, array('transactional'=> new Transactional())));

        return new MetaObject($service, $pluggables, false);
    }

    /**
     * @return \Zend\Log\Writer\Mock
     */
    private function getLoggerMock()
    {
        return self::$logger->getWriters()->toArray()[0];
    }

    public function testTransactionalNoTransaction()
    {
        $obj = $this->getTransactionalMethod();
        self::assertEquals('No Transaction', $obj->doSomethingWithoutTransaction());
        $result = array_slice($this->getLoggerMock()->events, -2, 2);
        self::assertEquals(array(), $result);
    }

    public function testTransactionalSuccess()
    {
        $obj = $this->getTransactionalMethod();
        self::assertEquals('Faz algo', $obj->doSomething());
        $result = array_slice($this->getLoggerMock()->events, -2, 2);
        self::assertEquals('BEGINNING TRANSACTION UNIT=[logger]', $result[0]['message']);
        self::assertEquals('COMMITING TRANSACTION UNIT=[logger]', $result[1]['message']);
    }

    public function testTransactionalException()
    {
        $obj = $this->getTransactionalMethod();
        try {
            $obj->doSomethingWrong();
        } catch (\Exception $e) {
            self::assertEquals('error', $e->getMessage());
        }
        $result = array_slice($this->getLoggerMock()->events, -2, 2);
        self::assertEquals('BEGINNING TRANSACTION UNIT=[logger]', $result[0]['message']);
        self::assertEquals('ROLLBACKING TRANSACTION UNIT=[logger]', $result[1]['message']);
    }

    public function testTransactionCompositeTransactions()
    {
        $obj = $this->getTransactionalMethod();
        $result = $obj->doSomethingComposite();
        self::assertEquals('OpComposite[OpTx1,OpTx2]', $result);
        $result = array_slice($this->getLoggerMock()->events, -6, 6);
        self::assertEquals('BEGINNING TRANSACTION UNIT=[compositeLogger]', $result[0]['message']);
        self::assertEquals('BEGINNING TRANSACTION UNIT=[logger]', $result[1]['message']);
        self::assertEquals('BEGINNING TRANSACTION UNIT=[logger2]', $result[2]['message']);
        self::assertEquals('COMMITING TRANSACTION UNIT=[logger2]', $result[3]['message']);
        self::assertEquals('COMMITING TRANSACTION UNIT=[logger]', $result[4]['message']);
        self::assertEquals('COMMITING TRANSACTION UNIT=[compositeLogger]', $result[5]['message']);
    }
}
