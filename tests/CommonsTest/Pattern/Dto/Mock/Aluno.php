<?php

namespace Commons\Pattern\Dto;

/**
 * Entidade Aluno [tipo complexo] (Mock).
 *
 * @category BeeTest
 * @package BeeTest\Service\Dto\Mock
 */
class Aluno extends Dto
{

    protected $mapping = array(
        'escola' => 'Commons\\Pattern\\Dto\\Escola',
        'matricula' => 'Commons\\Pattern\\Dto\\Matricula'
    );

    protected $aliases = array(
        'id' => 'id_aluno',
        'nome' => 'nome_aluno',
        'cpf' => 'cpf_aluno'
    );

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $nome;

    /**
     *
     * @var string
     */
    protected $cpf;

    /**
     *
     * @var Commons\Pattern\Dto\Escola
     */
    protected $escola;

    /**
     * Para testar 2 níveis e dependência cíclica.
     *
     * @var Commons\Pattern\Dto\Matricula
     */
    protected $matricula;

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
     * @return the $nome
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     *
     * @return the $cpf
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     *
     * @return the $escola
     */
    public function getEscola()
    {
        return $this->escola;
    }

    /**
     *
     * @return the $matricula
     */
    public function getMatricula()
    {
        return $this->matricula;
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
     * @param string $nome
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    /**
     *
     * @param string $cpf
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
    }

    /**
     *
     * @param \Commons\Pattern\Dto\Escola $escola
     */
    public function setEscola($escola)
    {
        $this->escola = $escola;
    }

    /**
     *
     * @param \Commons\Pattern\Dto\Matricula $matricula
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
    }
}
