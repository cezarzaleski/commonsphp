<?php

namespace Commons\Pattern\Paginator\Impl;

use Doctrine\ORM\EntityRepository;

/**
 * Paginador de repositório de entidades.
 */
class EntityPaginator extends ORMPaginator
{
    /**
     * Constructor
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $query = $repository->createQueryBuilder('x');
        parent::__construct(new \Doctrine\ORM\Tools\Pagination\Paginator($query));
    }
}
