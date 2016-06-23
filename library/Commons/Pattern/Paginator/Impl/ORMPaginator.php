<?php

namespace Commons\Pattern\Paginator\Impl;

use Commons\Pattern\Paginator\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * Implementação similar ao DoctrinePaginator do doctrine-orm-module para ZendFramework
 * porém sem a exposição do paginador do Doctrine nem a dependência do ZendFramework.
 */
class ORMPaginator implements Paginator
{
    /**
     * @var DoctrinePaginator
     */
    protected $paginator;

    /**
     * Constructor
     *
     * @param Paginator $paginator
     */
    public function __construct(DoctrinePaginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Paginator\Paginator::getItems()
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->paginator->getQuery()->setFirstResult($offset)->setMaxResults($itemCountPerPage);

        return $this->paginator->getIterator();
    }

    /**
     * {@inheritDoc}
     * @see Countable::count()
     */
    public function count($mode = null)
    {
        return $this->paginator->count();
    }
}
