<?php

namespace Commons\Pattern\Dto;

/**
 * Entidade Matricula [tipo complexo] (Mock).
 *
 * @category CommonsTest
 * @package CommonsTest\Pattern\Dto\Mock
 */
class Matricula extends Dto
{

    protected $mapping = array(
        'aluno' => 'Commons\\Pattern\\Dto\\Aluno',
        'escola' => 'Commons\\Pattern\\Dto\\Escola'
    );

    protected $id;

    /**
     *
     * @var string
     */
    protected $dataEntrada;

    /**
     *
     * @var Commons\Pattern\Dto\Aluno
     */
    protected $aluno;

    /**
     *
     * @var Commons\Pattern\Dto\Escola
     */
    protected $escola;

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
     * @return the $dataEntrada
     */
    public function getDataEntrada()
    {
        return $this->dataEntrada;
    }

    /**
     *
     * @return the $aluno
     */
    public function getAluno()
    {
        return $this->aluno;
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
     * @param \Commons\Pattern\Dto\mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param string $dataEntrada
     */
    public function setDataEntrada($dataEntrada)
    {
        $this->dataEntrada = $dataEntrada;
    }

    /**
     *
     * @param \Commons\Pattern\Dto\Aluno $aluno
     */
    public function setAluno($aluno)
    {
        $this->aluno = $aluno;
    }

    /**
     *
     * @param \Commons\Pattern\Dto\Escola $escola
     */
    public function setEscola($escola)
    {
        $this->escola = $escola;
    }
}
