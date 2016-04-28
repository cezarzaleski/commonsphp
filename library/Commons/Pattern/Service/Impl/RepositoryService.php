<?php

namespace Commons\Pattern\Service\Impl;

use Commons\Pattern\Di\LookupManager;
use Commons\Pattern\Repository\Repository;
use Psr\Log\LoggerInterface;

/**
 * Case base para criação de serviços de repositórios.
 */
class RepositoryService extends AbstractCoreService implements Repository
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param RepositoryInterface $repo
     * @param LookupInterface $lookupManager
     * @param LoggerInterface $logger
     */
    public function __construct(Repository $repo, LookupManager $lookupManager, LoggerInterface $logger)
    {
        parent::__construct($lookupManager, $logger);
        $this->repository = $repo;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Repository\Repository::delete()
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Repository\Repository::save()
     */
    public function save(array $data, $id = null)
    {
        return $this->repository->save($data, $id);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Repository\Repository::find()
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Repository\Repository::findAll()
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Repository\Repository::findBy()
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Repository\Repository::findOneBy()
     */
    public function findOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Repository\Repository::getClassName()
     */
    public function getClassName()
    {
        return $this->repository->getClassName();
    }

    /**
     * @return \Commons\Pattern\Repository\Repository
     */
    protected function getRepository()
    {
        return $this->repository;
    }
}
