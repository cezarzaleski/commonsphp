<?php

namespace Commons\Pattern\Paginator;

/**
 * Interface para definição de paginadores.
 */
interface Paginator extends \Countable
{
    /**
     * Returns a collection of items for a page.
     *
     * @param  int $offset Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage);
}
