<?php

namespace CommonsTest\Pattern\Dto;

// Necessário para simular o DtoInvalido com namespace Commons/Dto
require_once __DIR__ . '/Mock/Escola.php';
require_once __DIR__ . '/Mock/Aluno.php';
require_once __DIR__ . '/Mock/Matricula.php';

use CommonsTest\Pattern\Dto\Mock\EmailVazio;
use Commons\Pattern\Dto\Escola;
use Commons\Pattern\Dto\Aluno;

/**
 * Classe responsável por realizar testes para classe Dto.
 */
class DtoTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Método responsável por testar argumento nulo no construtor.
     */
    public function testConstrutor_sucessoArgumentoNulo()
    {
        $escola = new Escola();
        self::assertNull($escola->getId());
        self::assertNull($escola->getEmail());
        self::assertNull($escola->getEndereco());
        self::assertNull($escola->getNome());
        self::assertNull($escola->getNumeroTelefone());
    }

    /**
     * Método responável por testar argumentos incompatíveis no construtor
     * do Dto.
     *
     * @dataProvider argumentosInvalidos
     * @expectedException Commons\Exception\InvalidArgumentException
     * @expectedExceptionMessage Dados de inicialização de Dto inválidos.
     */
    public function testConstrutor_falhaArgumentoIncompativel($arg)
    {
        new Escola($arg);
    }

    /**
     * Retorna parâmetros inválidos para criação de dtos.
     *
     * @return mixed parâmetros inválidos.
     */
    public function argumentosInvalidos()
    {
        return array(
            array(
                fopen(__DIR__ . '/DtoTest.php', 'r')
            ),
            array(
                function ()
                {}
            )
        );
    }

    /**
     * Método responável por testar argumentos ignorados no construtor
     * do Dto.
     *
     * @dataProvider argumentosIgnorados
     */
    public function testConstrutor_sucessoArgumentoIgnorados($arg)
    {
        $instance = new Escola($arg);
        self::assertNotNull($instance);
    }

    /**
     * Retorna parâmetros escalares que devem ser ignorados na criação dos dtos.
     *
     * @return mixed parâmetros ignorados
     */
    public function argumentosIgnorados()
    {
        return array(
            array(
                'Hogwarts'
            ),
            array(
                123
            ),
            array(
                123.123
            ),
            array(
                true
            ),
            array(
                false
            )
        );
    }

    /**
     * Método responsável por testar a construção de Dtos com dados incompletos.
     */
    public function testConstrutor_sucessoDadosIncompletos()
    {
        $escolaDados = new \stdClass();
        $escolaDados->id = 1;
        $escolaDados->email = 'escolinha@mec.gov.br';
        $escolaDados->nome = 'Escolinha Trololo';

        $escola = new Escola($escolaDados);

        self::assertEquals(1, $escola->getId());
        self::assertEquals('escolinha@mec.gov.br', $escola->getEmail());
        self::assertEquals('Escolinha Trololo', $escola->getNome());
        self::assertNull($escola->getEndereco());
        self::assertNull($escola->getNumeroTelefone());
    }

    /**
     * Método responsável por testar a criação de um Dto a partir de dados avulsos
     * em \stdClass.
     */
    public function testConstrutor_sucessoDadosCompletos()
    {
        $escolaDados = new \stdClass();
        $escolaDados->id = 1;
        $escolaDados->email = 'escolinha@mec.gov.br';
        $escolaDados->nome = 'Escolinha Trololo';
        $escolaDados->endereco = 'Ed. Trololo, Rua Bla, Lote. 0, Trololo City';
        $escolaDados->numeroTelefone = '1234-4321';

        $escola = new Escola($escolaDados);
        self::assertEquals(1, $escola->getId());
        self::assertEquals('escolinha@mec.gov.br', $escola->getEmail());
        self::assertEquals('Escolinha Trololo', $escola->getNome());
        self::assertEquals('Ed. Trololo, Rua Bla, Lote. 0, Trololo City', $escola->getEndereco());
        self::assertEquals('1234-4321', $escola->getNumeroTelefone());
    }

    /**
     * Método responsável por testar a criação de Dtos através de dados
     * em array associativo.
     */
    public function testConstrutor_sucessoDadosArrayAssociativo()
    {
        $escolaDados = array(
            "id" => 1,
            "email" => 'escolinha@mec.gov.br',
            "nome" => 'Escolinha Trololo',
            "endereco" => 'Ed. Trololo, Rua Bla, Lote. 0, Trololo City',
            "numeroTelefone" => '1234-4321'
        );

        $escola = new Escola($escolaDados);
        self::assertEquals(1, $escola->getId());
        self::assertEquals('escolinha@mec.gov.br', $escola->getEmail());
        self::assertEquals('Escolinha Trololo', $escola->getNome());
        self::assertEquals('Ed. Trololo, Rua Bla, Lote. 0, Trololo City', $escola->getEndereco());
        self::assertEquals('1234-4321', $escola->getNumeroTelefone());
    }

    /**
     * Método responsável por testar a criação de um Dto a partir de
     * dados existentes em outro objeto.
     */
    public function testConstrutor_sucessoDadosObjeto()
    {
        $escolaDados = new Escola();
        $escolaDados->setId(1);

        $escola = new Escola($escolaDados);

        self::assertEquals(1, $escola->getId());
        self::assertNull($escola->getEmail());
        self::assertNull($escola->getNome());
        self::assertNull($escola->getEndereco());
        self::assertNull($escola->getNumeroTelefone());
    }

    /**
     * Método responsável por testar a criação de um Dto complexo
     */
    public function testConstrutor_sucessoTipoComplexoECiclico()
    {
        $escolaDados = new \stdClass();
        $escolaDados->id = 1;
        $escolaDados->email = 'escolinha@mec.gov.br';
        $escolaDados->nome = 'Escolinha Trololo';
        $escolaDados->endereco = 'Ed. Trololo, Rua Bla, Lote. 0, Trololo City';
        $escolaDados->numeroTelefone = '1234-4321';

        $matriculaDados = new \stdClass();
        $matriculaDados->id = 1;
        $matriculaDados->dataEntrada = '01/08/2012';
        $matriculaDados->escola = $escolaDados;

        $alunoDados = new \stdClass();
        $alunoDados->id = 1;
        $alunoDados->nome = 'Fulano';
        $alunoDados->cpf = 99999999999;
        $alunoDados->escola = $escolaDados;
        $alunoDados->matricula = $matriculaDados;

        // fecha ciclo
        $matriculaDados->aluno = $alunoDados;

        $aluno = new Aluno($alunoDados);

        self::assertInstanceOf('Commons\Pattern\Dto\Aluno', $aluno);
        self::assertInstanceOf('Commons\Pattern\Dto\Escola', $aluno->getEscola());
        self::assertInstanceOf('Commons\Pattern\Dto\Matricula', $aluno->getMatricula());
        self::assertInstanceOf('Commons\Pattern\Dto\Aluno', $aluno->getMatricula()->getAluno());
        self::assertInstanceOf('Commons\Pattern\Dto\Escola', $aluno->getMatricula()->getEscola());
        self::assertEquals($aluno, $aluno->getMatricula()->getAluno());
        self::assertEquals($aluno->getEscola(), $aluno->getMatricula()->getEscola());
    }

    /**
     * Método responsável por testar a criação de um Dto por dados avulsos
     * por aliases.
     */
    public function testConstrutor_sucessoTipoComplexoAliases()
    {
        $alunoDados = new \stdClass();
        $alunoDados->id_aluno = 2000;
        $alunoDados->nome_aluno = 'Sicrano';
        $alunoDados->cpf_aluno = 88888888888;

        $aluno = new Aluno($alunoDados);

        self::assertInstanceOf('Commons\Pattern\Dto\Aluno', $aluno);
        self::assertEquals(2000, $aluno->getId());
        self::assertEquals('Sicrano', $aluno->getNome());
        self::assertEquals(88888888888, $aluno->getCpf());
    }

    /**
     * Método responsável por testar a criação de um Dto a partir de um dado
     * com objeto que pode ser convertido em string.
     */
    public function testConstrutor_sucessoDadoObjetoConvertidoEmString()
    {
        $escolaDados = new \stdClass();
        $escolaDados->id = 1;
        $escolaDados->email = new EmailVazio();

        $escola = new Escola($escolaDados);
        self::assertEquals(1, $escola->getId());
        self::assertEmpty($escola->getEmail());
    }

    public function testImport()
    {
        $alunoDados = array(
            'idAluno' => 1,
            'nomeAluno' => 'Beltrano'
        );
        $aluno = new Aluno();

        $aluno->import($alunoDados, array(
                'id' => 'idAluno',
                'nome' => 'nomeAluno'
            ));

        self::assertEquals(1, $aluno->getId());
        self::assertEquals('Beltrano', $aluno->getNome());
    }

    public function testToArray()
    {
        $alunoDados = new \stdClass();
        $alunoDados->id_aluno = 2000;
        $alunoDados->nome_aluno = 'Sicrano';
        $alunoDados->cpf_aluno = 88888888888;

        $aluno = new Aluno($alunoDados);

        $arr = $aluno->toArray();

        self::assertEquals(2000, $arr['id']);
        self::assertEquals('Sicrano', $arr['nome']);
        self::assertEquals(88888888888, $arr['cpf']);
    }

    public function testImportFromArray()
    {
        $alunoDados = array(
            'idAluno' => 1,
            'nomeAluno' => 'Beltrano'
        );
        $aluno = new Aluno();

        $aluno->fromArray(
            array(
                'data' => $alunoDados,
                'aliases' => array(
                    'id' => 'idAluno',
                    'nome' => 'nomeAluno'
                )
            )
        );

        self::assertEquals(1, $aluno->getId());
        self::assertEquals('Beltrano', $aluno->getNome());
    }

    /**
     * @expectedException Commons\Exception\InvalidArgumentException
     * @expectedExceptionMessage O formato das opções está incorreto, o campo 'data' não é um array.
     */
    public function testImportFromArrayInvalidFormat()
    {
        $aluno = new Aluno();
        $aluno->fromArray(array('data' => null));
    }

    /**
     * @expectedException Commons\Exception\InvalidArgumentException
     * @expectedExceptionMessage O formato das opções está incorreto, o campo 'data' não é um array.
     */
    public function testImportFromArrayUnsettedProperty()
    {
        $aluno = new Aluno();
        $aluno->fromArray(array());
    }
}
