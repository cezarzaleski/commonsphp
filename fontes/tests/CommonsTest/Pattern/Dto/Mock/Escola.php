<?php

namespace Commons\Pattern\Dto;

/**
 * Entidade Escola (Mock).
 *
 * @category CommonsTest
 * @package CommonsTest\Pattern\Dto\Mock
 */
class Escola extends Dto
{

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var string
     */
    protected $nome;

    /**
     *
     * @var string
     */
    protected $numeroTelefone;

    /**
     *
     * @var string
     */
    protected $endereco;

    /**
     *
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *
     * @return the $nome
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     *
     * @return the $numeroTelefone
     */
    public function getNumeroTelefone()
    {
        return $this->numeroTelefone;
    }

    /**
     *
     * @return the $endereco
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     *
     * @param
     *            Ambigous <\Commons\Pattern\Dto\mixed, number> $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     *
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     *
     * @param string $numeroTelefone
     */
    public function setNumeroTelefone($numeroTelefone)
    {
        $this->numeroTelefone = $numeroTelefone;
    }

    /**
     *
     * @param string $endereco
     */
    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
    }
}
