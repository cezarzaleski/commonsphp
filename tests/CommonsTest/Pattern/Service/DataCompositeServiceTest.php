<?php
namespace CommonsTest\Pattern\Service;


use Commons\Pattern\Service\Impl\RepositoryService;
use Commons\Pattern\Service\Impl\ZendServiceLookupManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Zend\Di\ServiceLocator;
use Zend\Log\Logger;
use Zend\Log\PsrLoggerAdapter;

class DataCompositeServiceTest extends \PHPUnit_Framework_TestCase
{
    protected static $em = null;

    public static function setUpBeforeClass()
    {
        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__), $isDevMode, null, null, false);

        $connectionOptions = array('driver' => 'pdo_sqlite', 'memory' => true);

        // obtaining the entity manager
        self::$em =  EntityManager::create($connectionOptions, $config);

        $schemaTool = new SchemaTool(self::$em);

        $cmf = self::$em->getMetadataFactory();
        $classes = $cmf->getAllMetadata();

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
    }

    /**
     * @return \Commons\Pattern\Service\Impl\RepositoryService
     */
    public function getExemploService()
    {
        $repo = self::$em->getRepository('CommonsTest\Pattern\Service\Mock\ExemploEntity');
        $lookupManager = new ZendServiceLookupManager(new ServiceLocator());
        $logger = new PsrLoggerAdapter(new Logger());

        return new RepositoryService($repo, $lookupManager, $logger);
    }

    /**
     * @return \Commons\Pattern\Service\Impl\RepositoryService
     */
    public function getInstanciaExemploService()
    {
        $repo = self::$em->getRepository('CommonsTest\Pattern\Service\Mock\InstanciaExemploEntity');
        $lookupManager = new ZendServiceLookupManager(new ServiceLocator());
        $logger = new PsrLoggerAdapter(new Logger());

        return new RepositoryService($repo, $lookupManager, $logger);
    }

    /**
     * @return \CommonsTest\Pattern\Service\DataCompositeService
     */
    public function getDataCompositeService()
    {
        $lookupManager = new ZendServiceLookupManager(new ServiceLocator());
        $logger = new PsrLoggerAdapter(new Logger());

        return new DataCompositeService(self::$em, $lookupManager, $logger);
    }

    public function testRepositoryServiceSave()
    {
        $entity1 = $this->getExemploService()->save(array('name'=>'Tipo1'));
        $entity2 = $this->getExemploService()->save(array('name'=>'Tipo2'));

        $this->getInstanciaExemploService()->save(array('name'=>'ex1', 'tipoExemplo' => $entity1));
        $this->getInstanciaExemploService()->save(array('name'=>'ex2', 'tipoExemplo' => $entity1));
        $this->getInstanciaExemploService()->save(array('name'=>'ex1', 'tipoExemplo' => $entity2));

        $arr = $this->getDataCompositeService()->recuperarInstanciasExemploTipadas();

        self::assertEquals(3, \count($arr));
        self::assertEquals('ex1', $arr[0]['nome']);
        self::assertEquals('Tipo1', $arr[0]['tipo']);
        self::assertEquals('ex2', $arr[1]['nome']);
        self::assertEquals('Tipo1', $arr[1]['tipo']);
        self::assertEquals('ex1', $arr[2]['nome']);
        self::assertEquals('Tipo2', $arr[2]['tipo']);
    }
}
