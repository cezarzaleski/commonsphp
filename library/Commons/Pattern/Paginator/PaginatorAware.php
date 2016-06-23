<?php

namespace Commons\Pattern\Paginator;

/**
 * Interface que indica que o objeto ou coleção de objetos tem funções suficientes para
 * gerar um paginador de si mesmo.
 */
interface PaginatorAware
{
    /**
     * @return Paginator
     */
    public function createPaginator();
}
