<?php

namespace Commons\Pattern\Selector;

use Commons\Pattern\Criteria\Criteria;
use Commons\Exception\InvalidArgumentException;

/**
 * Seleciona um objeto contra outro baseado em um filtro ou o padrão.
 */
class Selector
{
    /**
     * Código de não encontrado.
     *
     * @var int
     */
    const NOT_FOUND = -1;

    /**
     * Array de selecionáveis.
     * @var array
     */
    protected $selectables = array();

    /**
     * Chave padrão de seleção.
     * @var string
     */
    private $defaultSelection = "default";

    /**
     * Construtor padrão.
     *
     * @param array $selectables
     * @param string $defaultSelection
     */
    public function __construct(array $selectables, $defaultSelection = "default")
    {
        $this->selectables = $selectables;
        $this->defaultSelection = $defaultSelection;
    }

    /**
     * Seleciona um objeto dado um filtro específico.
     * @param Commons\Pattern\Criteria\Criteria $filter
     * @return mixed
     * @throws InvalidArgumentException quando não encontrada a seleção padrão.
     */
    public function select(Criteria $criteria)
    {
        $selectable = $criteria->meetCriteria($this->selectables);
        if ($selectable == null || empty($selectable)) {
            if (isset($this->selectables[$this->defaultSelection])) {
                $selectable = $this->selectables[$this->defaultSelection];
            } else {
                throw new InvalidArgumentException('Not found.', Selector::NOT_FOUND);
            }
        }
        return $selectable;
    }
}
