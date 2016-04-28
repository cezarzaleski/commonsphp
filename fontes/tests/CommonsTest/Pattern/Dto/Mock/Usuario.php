<?php

namespace Commons\Pattern\Dto;

/**
 * Classe da entidade Usuário.
 *
 * @category Commons
 * @package Commons\Pattern\Dto
 */
class Usuario extends Dto
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
    protected $cpf;

    /**
     *
     * @var string
     */
    protected $nome;

    /**
     *
     * @var string
     */
    protected $nomeMae;

    /**
     *
     * @var string
     */
    protected $dataNascimento;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var string
     */
    protected $idInstituicao;

    /**
     *
     * @var string
     */
    protected $numeroCelular;

    /**
     *
     * @var string
     */
    protected $numeroTelefone;

    /**
     *
     * @var string
     */
    protected $idPerfil;

    /**
     *
     * @var string
     */
    protected $dataCancelamento;

    /**
     *
     * @var string
     */
    protected $idMotivoCancelamentoUsuario;

    /**
     *
     * @var string
     */
    protected $idSituacaoUsuario;

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
     * @return the $cpf
     */
    public function getCpf()
    {
        return $this->cpf;
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
     * @return the $nomeMae
     */
    public function getNomeMae()
    {
        return $this->nomeMae;
    }

    /**
     *
     * @return the $dataNascimento
     */
    public function getDataNascimento()
    {
        return $this->dataNascimento;
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
     * @return the $idInstituicao
     */
    public function getIdInstituicao()
    {
        return $this->idInstituicao;
    }

    /**
     *
     * @return the $numeroCelular
     */
    public function getNumeroCelular()
    {
        return $this->numeroCelular;
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
     * @return the $idPerfil
     */
    public function getIdPerfil()
    {
        return $this->idPerfil;
    }

    /**
     *
     * @return the $dataCancelamento
     */
    public function getDataCancelamento()
    {
        return $this->dataCancelamento;
    }

    /**
     *
     * @return the $idMotivoCancelamentoUsuario
     */
    public function getIdMotivoCancelamentoUsuario()
    {
        return $this->idMotivoCancelamentoUsuario;
    }

    /**
     *
     * @return the $idSituacaoUsuario
     */
    public function getIdSituacaoUsuario()
    {
        return $this->idSituacaoUsuario;
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
     * @param string $cpf
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
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
     * @param string $nomeMae
     */
    public function setNomeMae($nomeMae)
    {
        $this->nomeMae = $nomeMae;
    }

    /**
     *
     * @param string $dataNascimento
     */
    public function setDataNascimento($dataNascimento)
    {
        $this->dataNascimento = $dataNascimento;
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
     * @param string $idInstituicao
     */
    public function setIdInstituicao($idInstituicao)
    {
        $this->idInstituicao = $idInstituicao;
    }

    /**
     *
     * @param string $numeroCelular
     */
    public function setNumeroCelular($numeroCelular)
    {
        $this->numeroCelular = $numeroCelular;
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
     * @param string $idPerfil
     */
    public function setIdPerfil($idPerfil)
    {
        $this->idPerfil = $idPerfil;
    }

    /**
     *
     * @param string $dataCancelamento
     */
    public function setDataCancelamento($dataCancelamento)
    {
        $this->dataCancelamento = $dataCancelamento;
    }

    /**
     *
     * @param string $idMotivoCancelamentoUsuario
     */
    public function setIdMotivoCancelamentoUsuario($idMotivoCancelamentoUsuario)
    {
        $this->idMotivoCancelamentoUsuario = $idMotivoCancelamentoUsuario;
    }

    /**
     *
     * @param string $idSituacaoUsuario
     */
    public function setIdSituacaoUsuario($idSituacaoUsuario)
    {
        $this->idSituacaoUsuario = $idSituacaoUsuario;
    }
}
