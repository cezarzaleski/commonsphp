<?php

namespace CommonsTest\Pattern\Dto;

use Commons\Pattern\Dto\DtoAssembler;

// Necessário para simular o DtoInvalido com namespace Commons/Pattern/Dto
require_once __DIR__ . '/../Dto/Mock/DtoInvalido.php';
require_once __DIR__ . '/../Dto/Mock/Usuario.php';

/**
 * Classe responsável por realizar testes para a classe DtoAssembler.
 */
class DtoAssemblerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Método responsável por verificar a passagem de parâmetros inválidos
     * para criação de Dtos.
     *
     * @param mixed $data
     * @param string $class
     *            @dataProvider argumentosInvalidos
     *            @expectedException Commons\Exception\InvalidArgumentException
     */
    public function testCreate_falhaArgumentosInvalidos($class)
    {
        DtoAssembler::create(null, $class);
    }

    /**
     * Método responsável por verificar uma classe não aderente ao padrão.
     *
     * @expectedException Commons\Exception\InvalidArgumentException
     * @expectedExceptionMessage Classe DTO Commons\Pattern\Dto\DtoInvalido inválida.
     */
    public function testCreate_falhaDtoInvalido()
    {
        DtoAssembler::create(null, "Commons\\Pattern\\Dto\\DtoInvalido");
    }

    /**
     * Método responsável por testar a criação de objeto tipado
     * sem dados de entrada.
     */
    public function testCreate_sucessoSemDadosArrayVazio()
    {
        $instance = DtoAssembler::create(null, "Commons\\Pattern\\Dto\\Usuario");
        self::assertInstanceOf('Commons\\Pattern\\Dto\\Usuario', $instance);
    }

    /**
     * Método responsável por testar a criação de objeto tipado
     * sem dados de entrada (array()).
     */
    public function testCreate_sucessoSemDados()
    {
        $instance = DtoAssembler::create(array(), "Commons\\Pattern\\Dto\\Usuario");
        self::assertEquals(array(), $instance);
    }

    /**
     * Método responsável por testar a criação de objeto tipado a partir
     * de dados avulsos.
     */
    public function testCreate_sucessoDadosSimples()
    {
        $dados = new \stdClass();
        $dados->id = 1;
        $dados->cpf = 99999999999;
        $dados->dataNascimento = '25/07/2012';
        $dados->email = 'teste@mec.gov.br';
        $dados->idInstituicao = 1;
        $dados->nome = 'Usuario de Teste';
        $dados->numeroTelefone = 8888888;
        $dados->idPerfil = 1;

        $instance = DtoAssembler::create($dados, "Commons\\Pattern\\Dto\\Usuario");
        self::assertInstanceOf('Commons\\Pattern\\Dto\\Usuario', $instance);
        self::assertEquals(1, $dados->id);
        self::assertEquals(99999999999, $dados->cpf);
        self::assertEquals('25/07/2012', $dados->dataNascimento);
        self::assertEquals('teste@mec.gov.br', $dados->email);
        self::assertEquals(1, $dados->idInstituicao);
        self::assertEquals('Usuario de Teste', $dados->nome);
        self::assertEquals(8888888, $dados->numeroTelefone);
        self::assertEquals(1, $dados->idPerfil);
    }

    /**
     * Método responsável por testar a criação de um array de objetos tipado
     * através de dados avulsos.
     */
    public function testCreate_sucessoArrayDados()
    {
        $dados1 = new \stdClass();
        $dados1->id = 1;
        $dados1->cpf = 99999999999;
        $dados1->dataNascimento = '25/07/2012';
        $dados1->email = 'teste@mec.gov.br';
        $dados1->idInstituicao = 1;
        $dados1->nome = 'Usuario de Teste';
        $dados1->numeroTelefone = 8888888;
        $dados1->idPerfil = 1;

        $dados2 = new \stdClass();
        $dados2->id = 2;
        $dados2->cpf = 888888888888;
        $dados2->dataNascimento = '25/07/2012';
        $dados2->email = 'teste2@mec.gov.br';
        $dados2->idInstituicao = 2;
        $dados2->nome = 'Usuario de Teste 2';
        $dados2->numeroTelefone = 77777777;
        $dados2->idPerfil = 2;

        $dataArray = array(
            $dados1,
            $dados2
        );

        $instance = DtoAssembler::create($dataArray, "Commons\\Pattern\\Dto\\Usuario");
        self::assertInternalType('array', $instance);
        self::assertCount(2, $instance);

        self::assertInstanceOf('Commons\\Pattern\\Dto\\Usuario', $instance[0]);
        self::assertEquals(1, $instance[0]->getId());
        self::assertEquals(99999999999, $instance[0]->getCpf());
        self::assertEquals('25/07/2012', $instance[0]->getDataNascimento());
        self::assertEquals('teste@mec.gov.br', $instance[0]->getEmail());
        self::assertEquals(1, $instance[0]->getIdInstituicao());
        self::assertEquals('Usuario de Teste', $instance[0]->getNome());
        self::assertEquals(8888888, $instance[0]->getNumeroTelefone());
        self::assertEquals(1, $instance[0]->getIdPerfil());

        self::assertInstanceOf('Commons\\Pattern\\Dto\\Usuario', $instance[1]);
        self::assertEquals(2, $instance[1]->getId());
        self::assertEquals(888888888888, $instance[1]->getCpf());
        self::assertEquals('25/07/2012', $instance[1]->getDataNascimento());
        self::assertEquals('teste2@mec.gov.br', $instance[1]->getEmail());
        self::assertEquals(2, $instance[1]->getIdInstituicao());
        self::assertEquals('Usuario de Teste 2', $instance[1]->getNome());
        self::assertEquals(77777777, $instance[1]->getNumeroTelefone());
        self::assertEquals(2, $instance[1]->getIdPerfil());
    }

    /**
     * Método responsável por verificar a conformidade dos DTOs do projeto
     * com o padrão arquitetural adotado.
     *
     * @param string $dtoName
     *            nome curto do dto.
     *            @dataProvider dtosExistentes
     */
    public function testCreate_sucessoDtoConformidade($dtoName)
    {
        $instance = DtoAssembler::create(null, $dtoName);
        self::assertInstanceOf($dtoName, $instance);
    }

    /**
     * Retorna uma lista de parâmetros com os nomes curtos dos dtos existentes
     * no projeto.
     *
     * @return mixed array com nomes curtos dos Dtos existentes no projeto.
     */
    public function dtosExistentes()
    {
        $dtoClasses = array();

        $handler = opendir(__DIR__ . '/../Dto/Mock');
        while ($file = \readdir($handler)) {
            if (! is_dir($file)) {
                require_once __DIR__ . '/../Dto/Mock/' . $file;
                $className = str_replace('.php', '', $file);
                $fullName = 'Commons\\Pattern\\Dto\\' . $className;

                if (! $this->isExcludedClass($className) && ($fullName !== 'Commons\\Pattern\\Dto\\Dto')) {
                    $dtoClasses[] = array(
                        $fullName
                    );
                }
            }
        }
        closedir($handler);

        return $dtoClasses;
    }

    private function isExcludedClass($className)
    {
        // Classes que não extendem de Dto, nem possuem namespace 'Commons\Pattern\Dto\'
        $exclusionClasses = array(
            'DtoInvalido',
            'EmailVazio'
        );

        foreach ($exclusionClasses as $exclude) {
            if ($className === $exclude) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna conjunto de parâmetros inválidos para função de criação de Dto.
     *
     * @return mixed parâmetros inválidos.
     */
    public function argumentosInvalidos()
    {
        return array(
            array(
                null
            ),
            array(
                'class'
            ),
            array(
                1
            ),
            array(
                1.2
            ),
            array(
                true
            ),
            array(
                false
            )
        );
    }
}
