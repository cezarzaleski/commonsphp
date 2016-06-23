<?php

namespace Commons\Pattern\Paginator\Impl;

use Zend\Paginator\Adapter\AdapterInterface;
use Commons\Pattern\Paginator\Paginator;

/**
 * Classe responsável por adaptar a interface Paginator para AdapterInterface do ZendFramework.
 */
class ZendPaginatorAdapter implements AdapterInterface
{

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * Construtor padrão.
     */
    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * {@inheritDoc}
     * @see \Zend\Paginator\Adapter\AdapterInterface::getItems()
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->paginator->getItems($offset, $itemCountPerPage);
    }

    /**
     * {@inheritDoc}
     * @see Countable::count()
     */
    public function count($mode = null)
    {
        return $this->paginator->count($mode);
    }
}
