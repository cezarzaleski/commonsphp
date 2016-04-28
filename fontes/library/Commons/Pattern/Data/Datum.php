<?php
namespace Commons\Pattern\Data;

/**
 * Representa uma informação.
 */
interface Datum
{
    /**
     * Transforma a informação em uma representação de array.
     *
     * @return array
     */
    public function toArray();

    /**
     * Recompõe a informação através de uma representação de array.
     *
     * @param array $options
     */
    public function fromArray(array $options);
}
