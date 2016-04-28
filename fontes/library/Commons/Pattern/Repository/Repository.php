<?php

namespace Commons\Pattern\Repository;

/**
 * Interface responsável pelas operações básicas de um repositório.
 */
interface Repository
{
    /**
     * Roteador para insert e update
     *
     * @param array $data
     * @param interger|array $id
     * @return boolean
     */
    public function save(array $data, $id = null);

    /**
     * Apaga o registro "id" do banco
     *
     * @param integer|array $id
     * @return void
     */
    public function delete($id);

    /**
     * Encontra um objeto por sua chave primária/identificador.
     *
     * @param mixed $id Identificador.
     *
     * @return object o objeto.
     */
    public function find($id);

    /**
     * Encontra todos objetos no repositório.
     *
     * @return array os objetos.
     */
    public function findAll();

    /**
     * Encontra objetos de acordo com certos critérios.
     *
     * Opcionalmente detalhes de ordenação e limitação podem ser passados. Uma implementação poderá lançar
     * exceção \UnexpectedValueException se certos valores de ordenação e limitação não forem suportados.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array os objetos.
     *
     * @throws \UnexpectedValueException
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Encontra um único objeto para um conjunto de critérios.
     *
     * @param array $criteria os critérios.
     *
     * @return object o objeto.
     */
    public function findOneBy(array $criteria);

    /**
     * Retorna o nome da classe do objeto gerenciado pelo repositório.
     *
     * @return string
     */
    public function getClassName();
}
