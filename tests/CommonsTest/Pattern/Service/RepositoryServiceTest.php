<?php

namespace CommonsTest\Pattern\Service;

use Commons\Pattern\Repository\Impl\SimpleEntityRepository;
use Commons\Pattern\Service\Impl\RepositoryService;
use Commons\Pattern\Service\Impl\ZendServiceLookupManager;
use Commons\Pattern\Validator\Impl\AnnotationValidator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Zend\Di\ServiceLocator;
use Zend\Log\Logger;
use Zend\Log\PsrLoggerAdapter;

class RepositoryServiceTest extends \PHPUnit_Framework_TestCase
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

    public function getService()
    {
        $repo = self::$em->getRepository('CommonsTest\Pattern\Service\Mock\ExemploEntity');
        $repo->setValidatable(new AnnotationValidator());
        $lookupManager = new ZendServiceLookupManager(new ServiceLocator());
        $logger = new PsrLoggerAdapter(new Logger());

        return new RepositoryService($repo, $lookupManager, $logger);
    }

    public function testRepositoryServiceSave()
    {
        $entity = $this->getService()->save(array('name'=>'TesteUm'));
        self::assertNotEmpty($entity->getId());
        $entity = $this->getService()->save(array('name'=>'TesteDois'));
        self::assertNotEmpty($entity->getId());
    }

    public function testRepositoryServiceSaveAssertionFailStringMustNotContainNumbers()
    {
        try {
            $this->getService()->save(array('name'=>'Teste1'));
        } catch (\Commons\Exception\ServiceException $e) {
            self::assertNotEmpty($e->getMessages());
            self::assertTrue(\preg_match('/(.)*Must not contain numbers(.)*/', $e->getMessages()[0]) === 1);
        }
    }

    public function testRepositoryServiceSaveAssertionFailStringMinLimit()
    {
        try {
            $this->getService()->save(array('name'=>'a'));
        } catch (\Commons\Exception\ServiceException $e) {
            self::assertNotEmpty($e->getMessages());
            self::assertTrue(\preg_match('/(.)*At least 2 letters(.)*/', $e->getMessages()[0]) === 1);
        }
    }

    public function testRepositoryServiceSaveAssertionFailStringMaxLimit()
    {
        try {
            $this->getService()->save(array('name'=>'aaaaaaaaa aaaaaaaaa aaaaaaaaa aaaaaaaaa aaaaaaaaa b'));
        } catch (\Commons\Exception\ServiceException $e) {
            self::assertNotEmpty($e->getMessages());
            self::assertTrue(\preg_match('/(.)*Cannot exceed 50 letters and spaces(.)*/', $e->getMessages()[0]) === 1);
        }
    }

    public function testRepositoryServiceSaveAssertionMustNotBeNull()
    {
        try {
            $this->getService()->save(array('name'=>null));
        } catch (\Commons\Exception\ServiceException $e) {
            self::assertNotEmpty($e->getMessages());
            self::assertTrue(\preg_match('/(.)*This value should not be null(.)*/', $e->getMessages()[0]) === 1);
        }
    }

    public function testRepositoryServiceFind()
    {
        $entity = $this->getService()->find(1);
        self::assertEquals('TesteUm', $entity->getName());
    }

    public function testRepositoryServiceFindAll()
    {
        $arr = $this->getService()->findAll();
        self::assertEquals(2, count($arr));
    }

    public function testRepositoryServiceFindBy()
    {
        $arr = $this->getService()->findBy(array('name'=>'TesteDois'), array('name' => 'desc'), 1, 0);
        self::assertEquals(1, count($arr));
        self::assertEquals('TesteDois', $arr[0]->getName());
    }

    public function testRepositoryServiceFindOneBy()
    {
        $entity = $this->getService()->findOneBy(array('name'=>'TesteUm'));
        self::assertEquals('TesteUm', $entity->getName());
    }

    public function testRepositoryServiceGetClassName()
    {
        self::assertEquals('CommonsTest\Pattern\Service\Mock\ExemploEntity', $this->getService()->getClassName());
    }

    public function testRepositoryServiceProtectedGetRepository()
    {
        // esse método confere à classe extensora a capacidade de utilizar os métodos implementados no repositório.
        $reflectMethod = new \ReflectionMethod('Commons\Pattern\Service\Impl\RepositoryService::getRepository');
        $reflectMethod->setAccessible(true);
        $entityRepository = $reflectMethod->invoke($this->getService());
        self::assertTrue($entityRepository instanceof SimpleEntityRepository);
    }

    public function testRepositoryServiceDelete()
    {
        $this->getService()->delete(2);
        $entity = $this->getService()->find(2);
        self::assertNull($entity);
    }

    public static function tearDownAfterClass()
    {
        self::$em = null;
    }
}
