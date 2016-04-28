<?php
namespace Commons\Pattern\Criteria;

/**
 * Interface responsável por definir Critérios.
 */
interface Criteria
{
    /**
     * Retorna um array de itens que atendem ao critério proposto.
     * @param array $items
     * @return array|NULL filtered items
     */
    public function meetCriteria(array $items);
}
