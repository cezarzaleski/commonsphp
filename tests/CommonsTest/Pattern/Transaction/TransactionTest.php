<?php

namespace CommonsTest\Pattern\Transaction;

use CommonsTest\Pattern\Transaction\Mock\MockTransactionStrategy;
use CommonsTest\Pattern\Transaction\Mock\FailAdapterMock;
use Commons\Pattern\Transaction\Transaction;
use Commons\Pattern\Transaction\TransactionException;
use Commons\Pattern\Transaction\Strategy\AdapterTransactionStrategy;
use Commons\Pattern\Db\Feature\AdapterQueryMapFeature;
use Zend\Db\Adapter\Adapter as ZendAdapter;
use Zend\Cache\Storage\Adapter\FilesystemOptions;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Plugin\Serializer;
use Commons\Pattern\Cache\CacheZendAdapter;
use Commons\Pattern\Transaction\Strategy\TransactionStrategy;

class TransactionTest extends \PHPUnit_Framework_TestCase
{

    const DB1 = 'test-transaction-db1';

    const DB2 = 'test-transaction-db2';

    const DBERR = 'test-transaction-dberr';

    static $adapters = array();

    public static function createDataBaseFile($fileName)
    {
        // cria arquivo físico para banco de dados
        $file = sys_get_temp_dir() . $fileName;
        if (file_exists($file)) {
            unlink($file);
        }
        touch($file);
        return $file;
    }

    public static function createSqlLiteAdapter($dbName, $dbFile)
    {
        $db = new ZendAdapter(array(
            'driver' => 'Pdo_Sqlite',
            'database' => $dbFile,
            'sqlite2' => true
        ));
        self::$adapters[$dbName] = clone $db;
        Transaction::registerStrategy(new AdapterTransactionStrategy($db, $dbName));
    }

    protected function setUp()
    {
        self::$adapters[TransactionTest::DB1]->query('delete from fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        self::$adapters[TransactionTest::DB2]->query('delete from fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        self::$adapters[TransactionTest::DBERR]->query('delete from fruit', ZendAdapter::QUERY_MODE_EXECUTE);
    }

    public static function setUpBeforeClass()
    {
        Transaction::initialize();

        // cria arquivo para banco de dados 1
        $fileDb1 = self::createDataBaseFile('/commons-test-transaction-db1.sqlite');

        // cria arquivo para banco de dados 2
        $fileDb2 = self::createDataBaseFile('/commons-test-transaction-db2.sqlite');

        // cria arquivo para o banco de dados com adaptador estragado
        $fileDbErr = self::createDataBaseFile('/commons-test-transaction-dberr.sqlite');

        // criar adapter para banco de dados
        self::createSqlLiteAdapter(TransactionTest::DB1, $fileDb1);
        self::createSqlLiteAdapter(TransactionTest::DB2, $fileDb2);

        $db = new FailAdapterMock(array(
            'driver' => 'Pdo_Sqlite',
            'database' => $fileDbErr,
            'sqlite2' => true
        ));
        Transaction::registerStrategy(new AdapterTransactionStrategy($db, TransactionTest::DBERR));
        self::$adapters[TransactionTest::DBERR] = clone $db;

        self::$adapters[TransactionTest::DB1]->query('create table fruit (name varchar(100) primary key)', ZendAdapter::QUERY_MODE_EXECUTE);
        self::$adapters[TransactionTest::DB2]->query('create table fruit (name varchar(100) primary key)', ZendAdapter::QUERY_MODE_EXECUTE);
        self::$adapters[TransactionTest::DBERR]->query('create table fruit (name varchar(100) primary key)', ZendAdapter::QUERY_MODE_EXECUTE);

        $cache = self::createCache();

        $featureDb1 = new AdapterQueryMapFeature(Transaction::getStrategy(TransactionTest::DB1)->getAssigner(), $cache, __DIR__ . '/Mock');
        Transaction::getStrategy(TransactionTest::DB1)->setFeature($featureDb1);
        $featureDb2 = new AdapterQueryMapFeature(Transaction::getStrategy(TransactionTest::DB2)->getAssigner(), $cache, __DIR__ . '/Mock');
        Transaction::getStrategy(TransactionTest::DB2)->setFeature($featureDb2);
        $featureDberr = new AdapterQueryMapFeature(Transaction::getStrategy(TransactionTest::DBERR)->getAssigner(), $cache, __DIR__ . '/Mock');
        Transaction::getStrategy(TransactionTest::DBERR)->setFeature($featureDberr);
    }

    private static function createCache()
    {
        $options = new FilesystemOptions();
        $options->setTtl(120);
        $options->setCacheDir(sys_get_temp_dir());
        $options->setNamespace('Cache');
        $cache = new Filesystem($options);
        $cache->addPlugin(new Serializer());
        $cache->clearByNamespace('Cache');
        return new CacheZendAdapter($cache);
    }

    public static function tearDownAfterClass()
    {
        Transaction::unregisterStrategy(TransactionTest::DB1);
        Transaction::unregisterStrategy(TransactionTest::DB2);
        Transaction::unregisterStrategy(TransactionTest::DBERR);
    }

    public function testDemacarcaoFalhaSemCallback()
    {
        try {
            Transaction::demarcate(TransactionTest::DB1, 'notACallbak');
        } catch (\Exception $e) {
            self::assertEquals('Callback ou closure deve ser definida.', $e->getMessage());
        }
    }

    public function testDemarcacaoMalSucedida()
    {
        $bindFruit = array(
            ':value' => 'uva'
        );

        // realiza insert que é cancelado
        try {
            Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruit)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruit);
                throw new TransactionException("Provocar Rollback", 0, null);
            });
            $this->fail('Deverá ser lançada a exceção.');
        } catch (\Exception $e) {}

        // verifica que se a fruta não foi inserida pois ocorreu um erro
        $proxy = Transaction::getStrategy(TransactionTest::DB1);
        $fruit = $proxy->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEmpty($fruit);
    }

    public function testDemarcacaoBemSucedida()
    {
        $bindFruit = array(
            ':value' => 'melão'
        );

        // realiza insert transacionado.
        Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruit)
        {
            $strategy->getFeature()->query('i_fruit', $bindFruit);
        });

        // verifica que se a fruta foi inserida
        $fruit = Transaction::getStrategy(TransactionTest::DB1)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruit[':value'], $fruit[0]['name']);
    }

    public function testDemarcacaoEmCadeiaMesmoBancoMalSucedido()
    {
        $bindFruit1 = array(
            ':value' => 'fruta do conde'
        );
        $bindFruit2 = array(
            ':value' => 'amora'
        );

        try {
            Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruit1, $bindFruit2)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruit1);

                // demarca outra transação para o banco 1
                Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruit2)
                {
                    $strategy->getFeature()->query('i_fruit', $bindFruit2);
                    throw new TransactionException("Provocar Rollback", 0, null);
                });
            });
            $this->fail('Deverá ser lançada a exceção.');
        } catch (\Exception $e) {}

        // verifica que as frutas não foram inseridas no banco 1.
        $fruits = Transaction::getStrategy(TransactionTest::DB1)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruits = $fruits->toArray();
        self::assertEmpty($fruits);
    }

    public function testDemarcacaoEmCadeiaMesmoBancoComTratamentoInterno()
    {
        $bindFruit = array(
            ':value' => 'pêssego'
        );
        $log = null;

        try {
            $log = Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruit)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruit);

                // demarca outra transação para o banco 1
                // essa operação irá falhar pois tentará inserir a mesma fruta (pk constraint)
                try {
                    Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruit)
                    {
                        $strategy->getFeature()->query('i_fruit', $bindFruit);
                    });
                } catch (\Exception $e) {
                    return 'pk_constraint_error';
                }
            });
        } catch (\Exception $e) {
            $this->fail('Não deve ocorrer nenhum tipo de erro.');
        }

        // verifica que nenhum operação foi realizada pois ocorreu uma falha na transação.
        $fruit = Transaction::getStrategy(TransactionTest::DB1)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEmpty($fruit);
        self::assertEquals('pk_constraint_error', $log);
    }

    public function testDemarcacaoEmCadeiaMesmoBancoBemSucedido()
    {
        $bindFruit1 = array(
            ':value' => 'côco'
        );
        $bindFruit2 = array(
            ':value' => 'caju'
        );

        try {
            Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruit1, $bindFruit2)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruit1);

                // demarca outra transação para o banco 1
                Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruit2)
                {
                    $strategy->getFeature()->query('i_fruit', $bindFruit2);
                });
            });
        } catch (\Exception $e) {
            $this->fail('Não deve ocorrer nenhum tipo de erro.');
        }

        // verifica que as frutas foram inseridas no banco 1.
        $fruits = Transaction::getStrategy(TransactionTest::DB1)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruits = $fruits->toArray();
        self::assertNotEmpty($fruits);
        self::assertEquals($bindFruit1[':value'], $fruits[0]['name']);
        self::assertEquals($bindFruit2[':value'], $fruits[1]['name']);
    }

    public function testDemarcacaoEmCadeiaBancosDistintosMalSucedido()
    {
        $bindFruitDb1 = array(
            ':value' => 'pêra'
        );
        $bindFruitDb2 = array(
            ':value' => 'damasco'
        );

        try {
            Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruitDb1, $bindFruitDb2)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruitDb1);

                // demarca uma transação para o banco DB2
                Transaction::demarcate(TransactionTest::DB2, function (TransactionStrategy $strategy) use($bindFruitDb2)
                {
                    $strategy->getFeature()->query('i_fruit', $bindFruitDb2);
                    throw new TransactionException("Provocar Rollback", 0, null);
                });
            });
            $this->fail('Deverá ser lançada a exceção.');
        } catch (\Exception $e) {}

        // verifica que se a fruta não foi inserida nem no banco 1...
        $fruit = Transaction::getStrategy(TransactionTest::DB1)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEmpty($fruit);

        // nem no banco 2.
        $fruit = Transaction::getStrategy(TransactionTest::DB2)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEmpty($fruit);
    }

    public function testDemarcacaoEmCadeiaBancosDistintosErroAdaptador()
    {
        $bindFruitDb1 = array(
            ':value' => 'ata'
        );
        $bindFruitDb2 = array(
            ':value' => 'nectarina'
        );

        try {
            Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruitDb1, $bindFruitDb2)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruitDb1);

                // demarca uma transação para o banco DBERR que não ocorrerá por erros no commit e rollback
                Transaction::demarcate(TransactionTest::DBERR, function (TransactionStrategy $strategy) use($bindFruitDb2)
                {
                    $strategy->getFeature()->query('i_fruit', $bindFruitDb2);
                });
            });
            $this->fail('Deverá ser lançada a exceção.');
        } catch (\Exception $e) {}

        // verifica que se a fruta não foi inserida nem no banco 1...
        $fruit = Transaction::getStrategy(TransactionTest::DB1)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEmpty($fruit);

        // nem no banco 2.
        $fruit = Transaction::getStrategy(TransactionTest::DBERR)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEmpty($fruit);
    }

    public function testDemarcacaoEmCadeiaBancosDistintosBemSucedido()
    {
        $bindFruitDb1 = array(
            ':value' => 'figo'
        );
        $bindFruitDb2 = array(
            ':value' => 'kiwi'
        );

        try {
            Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruitDb1, $bindFruitDb2)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruitDb1);

                // demarca uma transação para o banco DB2
                Transaction::demarcate(TransactionTest::DB2, function (TransactionStrategy $strategy) use($bindFruitDb2)
                {
                    $strategy->getFeature()->query('i_fruit', $bindFruitDb2);
                });
            });
        } catch (\Exception $e) {
            $this->fail('Não deve ocorrer nenhum tipo de erro.');
        }

        // verifica que se a fruta foi inserida no banco 1...
        $fruit = Transaction::getStrategy(TransactionTest::DB1)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruitDb1[':value'], $fruit[0]['name']);

        // e no banco 2.
        $fruit = Transaction::getStrategy(TransactionTest::DB2)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruitDb2[':value'], $fruit[0]['name']);
    }

    public function testDemarcacaoEmCadeiaBancosDistintosComTratamentoInterno()
    {
        $bindFruitDb1 = array(
            ':value' => 'laranja'
        );
        $bindFruitDb2 = array(
            ':value' => 'bacon'
        );
        $log = null;

        try {
            $log = Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruitDb1, $bindFruitDb2)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruitDb1);

                // demarca uma transação para o banco DB2 mas ocorrerá um erro que será tratado
                try {
                    Transaction::demarcate(TransactionTest::DB2, function (TransactionStrategy $strategy) use($bindFruitDb2)
                    {
                        $strategy->getFeature()->query('i_fruit', $bindFruitDb2);
                        if ($bindFruitDb2[':value'] == 'bacon') {
                            throw new TransactionException("Bacon não é fruta", 0, null);
                        }
                    });
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            });
        } catch (\Exception $e) {
            $this->fail('Não deve ocorrer nenhum tipo de erro.');
        }

        // verifica que se a fruta foi inserida no banco 1...
        $fruit = Transaction::getStrategy(TransactionTest::DB1)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruitDb1[':value'], $fruit[0]['name']);

        // Mas não foi inserida no banco 2.
        $fruit = Transaction::getStrategy(TransactionTest::DB2)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEmpty($fruit);
        self::assertEquals('Bacon não é fruta', $log);
    }

    public function testDemarcaEmSequenciaBancosDistintos()
    {
        $bindFruitDb1 = array(
            ':value' => 'tomate'
        );
        $bindFruitDb2 = array(
            ':value' => 'caqui'
        );

        try {
            Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruitDb1)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruitDb1);
            });
        } catch (\Exception $e) {
            $this->fail('Não deve ocorrer nenhum tipo de erro.');
        }

        try {
            Transaction::demarcate(TransactionTest::DB2, function (TransactionStrategy $strategy) use($bindFruitDb2)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruitDb2);
            });
        } catch (\Exception $e) {
            $this->fail('Não deve ocorrer nenhum tipo de erro.');
        }

        // verifica que se a fruta foi inserida no banco 1...
        $fruit = Transaction::getStrategy(TransactionTest::DB1)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruitDb1[':value'], $fruit[0]['name']);

        // e no banco 2.
        $fruit = Transaction::getStrategy(TransactionTest::DB2)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruitDb2[':value'], $fruit[0]['name']);
    }

    public function testDemarcaEmSequenciaBancosDistintosComTratamento()
    {
        $bindFruitDb1 = array(
            ':value' => 'lima'
        );
        $bindFruitDb2 = array(
            ':value' => 'limão'
        );

        try {
            Transaction::demarcate(TransactionTest::DB1, function (TransactionStrategy $strategy) use($bindFruitDb1)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruitDb1);
                throw new TransactionException("Provocar Rollback", 0, null);
            });
            $this->fail('Deverá ser lançada a exceção.');
        } catch (\Exception $e) {}

        try {
            Transaction::demarcate(TransactionTest::DB2, function (TransactionStrategy $strategy) use($bindFruitDb2)
            {
                $strategy->getFeature()->query('i_fruit', $bindFruitDb2);
            });
        } catch (\Exception $e) {
            $this->fail('Não deve ocorrer nenhum tipo de erro.');
        }

        // verifica que se a fruta não foi inserida no banco 1 pois ocorreu uma exceção.
        $fruit = Transaction::getStrategy(TransactionTest::DB1)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEmpty($fruit);

        // No banco 2 executará normalmente.
        $fruit = Transaction::getStrategy(TransactionTest::DB2)->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruitDb2[':value'], $fruit[0]['name']);
    }

    /**
     * @expectedException Commons\Pattern\Transaction\TransactionException
     * @expectedExceptionMessage Não pode finalizar uma transação não inicializada.
     */
    public function testNaoRealizaCommitSemInicializacao()
    {
        Transaction::commit();
    }

    /**
     * @expectedException Commons\Pattern\Transaction\TransactionException
     * @expectedExceptionMessage Não pode finalizar uma transação não inicializada.
     */
    public function testNaoRealizaRollbackSemInicializacao()
    {
        Transaction::rollback();
    }

    /**
     * @expectedException Commons\Pattern\Transaction\TransactionException
     * @expectedExceptionMessage Não é possível remover uma estrátegia transacional enquanto existir uma transação ativa.
     */
    public function testRemoveAdaptadorDaTransacaoAtiva()
    {
        Transaction::demarcate(TransactionTest::DBERR, function ()
        {
            Transaction::unregisterStrategy(TransactionTest::DBERR);
        });
    }

    public function testFechaAdaptadores()
    {
        Transaction::closeStrategies();
        self::assertFalse(Transaction::getStrategy(TransactionTest::DB1)->getAssigner()
            ->getDriver()
            ->getConnection()
            ->isConnected());
        self::assertFalse(Transaction::getStrategy(TransactionTest::DB2)->getAssigner()
            ->getDriver()
            ->getConnection()
            ->isConnected());
        self::assertFalse(Transaction::getStrategy(TransactionTest::DBERR)->getAssigner()
            ->getDriver()
            ->getConnection()
            ->isConnected());
    }

    public function testRemoveAdaptadores()
    {
        Transaction::unregisterStrategy(TransactionTest::DB1);
        Transaction::unregisterStrategy(TransactionTest::DB2);
        Transaction::unregisterStrategy(TransactionTest::DBERR);
        self::assertTrue(! Transaction::isRegistered(TransactionTest::DB1));
        self::assertTrue(! Transaction::isRegistered(TransactionTest::DB2));
        self::assertTrue(! Transaction::isRegistered(TransactionTest::DBERR));
    }

    public function testEscopoTransacionalNuloAposDemarcacoes()
    {
        $class = new \ReflectionClass('Commons\Pattern\Transaction\Transaction');
        $currentScope = $class->getProperty('currentScope');
        $currentScope->setAccessible(true);
        $lastScope = $class->getProperty('lastScope');
        $lastScope->setAccessible(true);
        self::assertTrue($currentScope->getValue() === null);
        self::assertTrue($lastScope->getValue() === null);
    }

    public function testDemarcacaoSemProgramacaoFuncional()
    {
        $bindFruitDb1 = array(
            ':value' => 'goiaba'
        );
        $bindFruitDb2 = array(
            ':value' => 'melão'
        );

        Transaction::initialize();
        Transaction::resolveTransactionActivation(new AdapterTransactionStrategy(self::$adapters[TransactionTest::DB1], TransactionTest::DB1));
        Transaction::resolveTransactionActivation(new AdapterTransactionStrategy(self::$adapters[TransactionTest::DB2], TransactionTest::DB2));

        $cache = self::createCache();
        $featureDb1 = new AdapterQueryMapFeature(Transaction::getStrategy(TransactionTest::DB1)->getAssigner(), $cache, __DIR__ . '/Mock');
        Transaction::getStrategy(TransactionTest::DB1)->setFeature($featureDb1);
        $featureDb2 = new AdapterQueryMapFeature(Transaction::getStrategy(TransactionTest::DB2)->getAssigner(), $cache, __DIR__ . '/Mock');
        Transaction::getStrategy(TransactionTest::DB2)->setFeature($featureDb2);
        $proxy1 = Transaction::getStrategy(TransactionTest::DB1);
        $proxy2 = Transaction::getStrategy(TransactionTest::DB2);

        try {
            // inicia transação para banco DB1
            Transaction::beginTransaction(TransactionTest::DB1);

            // Realiza operações no DB1 (escopo atual é DB1, por isso não é necessário especificar)
            $proxy1->getFeature()->query('i_fruit', $bindFruitDb1);

            try {
                // inicia transação para banco DB2
                Transaction::beginTransaction(TransactionTest::DB2);

                // Realiza operações no DB2 (escopo atual é DB2)
                $proxy2->getFeature()->query('i_fruit', $bindFruitDb2);

                // Marca sucesso (commit deferido pois está dentro de outra transação [DB1])
                Transaction::commit();
            } catch (\Exception $e) {
                // Caso ocorra erros deverá marcar falha com rollback...
                Transaction::rollback();
                // e relançar a exceção ou tratá-la.
                throw $e;
            }
            // Marca sucesso (commit real pois é a finalização da transação pai)
            Transaction::commit();
        } catch (\Exception $e) {
            throw $e;
            $this->fail('Não deve ocorrer nenhum tipo de erro.');
            // Caso ocorram erros deve-se realizar o rollback.
            Transaction::rollback();
        }

        // verifica que se a fruta foi inserida no banco 1...
        $fruit = $proxy1->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruitDb1[':value'], $fruit[0]['name']);

        // e no banco 2.
        $fruit = $proxy2->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruitDb2[':value'], $fruit[0]['name']);
    }

    public function testDemarcacoesMisturadasBancosDistintos()
    {
        $bindFruitDb1 = array(
            ':value' => 'toranja'
        );
        $bindFruitDb2 = array(
            ':value' => 'tomate'
        );

        Transaction::initialize();
        Transaction::resolveTransactionActivation(new AdapterTransactionStrategy(self::$adapters[TransactionTest::DB1], TransactionTest::DB1));
        Transaction::resolveTransactionActivation(new AdapterTransactionStrategy(self::$adapters[TransactionTest::DB2], TransactionTest::DB2));
        $cache = self::createCache();
        $featureDb1 = new AdapterQueryMapFeature(Transaction::getStrategy(TransactionTest::DB1)->getAssigner(), $cache, __DIR__ . '/Mock');
        Transaction::getStrategy(TransactionTest::DB1)->setFeature($featureDb1);
        $featureDb2 = new AdapterQueryMapFeature(Transaction::getStrategy(TransactionTest::DB2)->getAssigner(), $cache, __DIR__ . '/Mock');
        Transaction::getStrategy(TransactionTest::DB2)->setFeature($featureDb2);

        $proxy1 = Transaction::getStrategy(TransactionTest::DB1);
        $proxy2 = Transaction::getStrategy(TransactionTest::DB2);
        try {
            // inicia transação para banco DB1
            Transaction::beginTransaction(TransactionTest::DB1);

            // Realiza operações no DB1 (escopo atual é DB1, por isso não é necessário especificar)
            $proxy1->getFeature()->query('i_fruit', $bindFruitDb1);

            Transaction::demarcate($proxy2->getName(), function (TransactionStrategy $strategy) use($bindFruitDb2)
            {
                // Realiza operações no DB2 (escopo atual é DB2)
                $strategy->getFeature()->query('i_fruit', $bindFruitDb2);
            });

            // Marca sucesso (commit real pois é a finalização da transação pai)
            Transaction::commit();
        } catch (\Exception $e) {
            throw $e;
            $this->fail('Não deve ocorrer nenhum tipo de erro.');
            // Caso ocorram erros deve-se realizar o rollback.
            Transaction::rollBack();
        }

        // verifica que se a fruta foi inserida no banco 1...
        $fruit = $proxy1->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruitDb1[':value'], $fruit[0]['name']);

        // e no banco 2.
        $fruit = $proxy2->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruitDb2[':value'], $fruit[0]['name']);
    }

    public function testDemarcacoesMisturadasMesmoBanco()
    {
        $bindFruit1 = array(
            ':value' => 'caqui'
        );
        $bindFruit2 = array(
            ':value' => 'baga'
        );

        Transaction::initialize();
        Transaction::resolveTransactionActivation(new AdapterTransactionStrategy(self::$adapters[TransactionTest::DB1], TransactionTest::DB1));
        $cache = self::createCache();
        $featureDb1 = new AdapterQueryMapFeature(Transaction::getStrategy(TransactionTest::DB1)->getAssigner(), $cache, __DIR__ . '/Mock');
        Transaction::getStrategy(TransactionTest::DB1)->setFeature($featureDb1);

        $proxy1 = Transaction::getStrategy(TransactionTest::DB1);
        try {
            // inicia transação para banco DB1
            Transaction::beginTransaction(TransactionTest::DB1);

            // Realiza operações no DB1 (escopo atual é DB1, por isso não é necessário especificar)
            $proxy1->getFeature()->query('i_fruit', $bindFruit1);

            Transaction::demarcate($proxy1->getName(), function (TransactionStrategy $strategy) use($bindFruit2)
            {
                // Realiza operações no DB2 (escopo atual é DB2)

                $strategy->getFeature()->query('i_fruit', $bindFruit2);
            });

            // Marca sucesso (commit real pois é a finalização da transação pai)
            Transaction::commit();
        } catch (\Exception $e) {
            throw $e;
            $this->fail('Não deve ocorrer nenhum tipo de erro.');
            // Caso ocorram erros deve-se realizar o rollback.
            Transaction::rollBack();
        }

        // verifica que se a fruta foi inserida no banco 1...
        $fruits = $proxy1->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruits = $fruits->toArray();
        self::assertNotEmpty($fruits);
        self::assertEquals($bindFruit1[':value'], $fruits[0]['name']);
        self::assertEquals($bindFruit2[':value'], $fruits[1]['name']);
    }

    public function testDemarcacoesMisturadasEstrategiasDistintas()
    {
        $bindFruitDb1 = array(
            ':value' => 'toranja'
        );

        Transaction::registerStrategy(new MockTransactionStrategy());
        $proxy1 = Transaction::getStrategy(TransactionTest::DB1);
        try {
            // inicia transação para banco DB1
            Transaction::beginTransaction(TransactionTest::DB1);

            // Realiza operações no DB1 (escopo atual é DB1, por isso não é necessário especificar)
            $proxy1->getFeature()->query('i_fruit', $bindFruitDb1);

            $testCase = $this;
            Transaction::demarcate(MockTransactionStrategy::MOCK_TRANSACTION, function (TransactionStrategy $strategy) use($testCase)
            {
                // pega a transação do scopo corrente
                $mts = Transaction::getStrategy();

                // deve ser a mesma que vem na demarcação da transação.
                $testCase->assertEquals($strategy, $mts);

                // em MockTransactionStrategy o assigner é nulo
                $testCase->assertNull($mts->getAssigner());

                // trabalho iniciado
                $testCase->assertEquals('String Mocking', $mts->work);
            });

            // MockTransactionStrategy não realizou o commit de verdade...
            $strategy = Transaction::getStrategy(MockTransactionStrategy::MOCK_TRANSACTION);
            self::assertEquals('String Mocking', $strategy->work);

            // Marca sucesso (commit real pois é a finalização da transação pai)
            Transaction::commit();
        } catch (\Exception $e) {
            throw $e;
            $this->fail('Não deve ocorrer nenhum tipo de erro.');
            // Caso ocorram erros deve-se realizar o rollback.
            Transaction::rollBack();
        }

        // verifica que se a fruta foi inserida no banco 1...
        $fruit = $proxy1->getFeature()->query('s_fruit', ZendAdapter::QUERY_MODE_EXECUTE);
        $fruit = $fruit->toArray();
        self::assertEquals($bindFruitDb1[':value'], $fruit[0]['name']);

        // Verifica se MockTransactionStrategy realizou o commit de suas operações
        $strategy = Transaction::getStrategy(MockTransactionStrategy::MOCK_TRANSACTION);
        self::assertEquals('String Mocking commiting...', $strategy->work);
        Transaction::unregisterStrategy(MockTransactionStrategy::MOCK_TRANSACTION);
    }

    /**
     * @expectedException Commons\Pattern\Transaction\TransactionException
     * @expectedExceptionMessage Não existe estratégia transacional registrada.
     */
    public function testInitialize()
    {
        Transaction::registerStrategy(new MockTransactionStrategy());
        Transaction::initialize();
        self::assertNull(Transaction::getStrategy(MockTransactionStrategy::MOCK_TRANSACTION));
    }

    public function testRegisterStrategy()
    {
        Transaction::initialize();
        $strategy = new MockTransactionStrategy();
        Transaction::registerStrategy($strategy);
        self::assertTrue(Transaction::isRegistered(MockTransactionStrategy::MOCK_TRANSACTION));
        self::assertTrue(Transaction::isRegistered($strategy));
        Transaction::unregisterStrategy($strategy);
        self::assertFalse(Transaction::isRegistered($strategy));
        Transaction::initialize();
    }

    public function testVerifyActiveTransaction()
    {
        Transaction::initialize();
        $strategy = new MockTransactionStrategy();
        Transaction::registerStrategy($strategy);
        self::assertFalse(Transaction::verifyActiveTransaction($strategy));
        Transaction::beginTransaction(MockTransactionStrategy::MOCK_TRANSACTION);
        self::assertTrue(Transaction::verifyActiveTransaction(MockTransactionStrategy::MOCK_TRANSACTION));
        Transaction::commit();
        Transaction::unregisterStrategy($strategy);
        self::assertFalse(Transaction::isRegistered($strategy));
        Transaction::initialize();
    }

    /**
     * @expectedException Commons\Pattern\Transaction\TransactionException
     * @expectedExceptionMessage Não é permitido registrar mesma estratégia com nome distinto.
     */
    public function testRegisterStrategyEstrategiasIguaisNomeDistinto()
    {
        Transaction::initialize();
        $strategy = new MockTransactionStrategy();
        Transaction::registerStrategy($strategy);
        $strategy->setName('alternative name');
        Transaction::registerStrategy($strategy);
        Transaction::unregisterStrategy($strategy);
        Transaction::initialize();
    }

    /**
     * @expectedException Commons\Pattern\Transaction\TransactionException
     * @expectedExceptionMessage Não é permitido registrar estratégias diferentes com o mesmo nome.
     */
    public function testRegisterStrategyEstrategiasDistintasNomesIguais()
    {
        Transaction::initialize();
        Transaction::registerStrategy(new MockTransactionStrategy());
        Transaction::registerStrategy(new MockTransactionStrategy());
        Transaction::initialize();
    }

    /**
     * @expectedException Commons\Pattern\Transaction\TransactionException
     * @expectedExceptionMessage Não é permitido registrar estratégias diferentes com o mesmo delegador.
     */
    public function testRegisterStrategyEstrategiasDistintasAssignerIguais()
    {
        Transaction::initialize();
        $assigner = 'Assigner';
        $strategy1 = new MockTransactionStrategy();
        $strategy1->setAssigner($assigner);
        $strategy2 = new MockTransactionStrategy();
        $strategy2->setAssigner($assigner);
        $strategy2->setName('Alternate Strategy');
        Transaction::registerStrategy($strategy1);
        Transaction::registerStrategy($strategy2);
        Transaction::initialize();
    }
}
