<?php

namespace CommonsTest\Util\Reflect;

// Necessário para simular o DtoInvalido com namespace Commons/Pattern/Dto
require_once __DIR__ . '/../../Pattern/Dto/Mock/Escola.php';
require_once __DIR__ . '/../../Pattern/Dto/Mock/Aluno.php';
require_once __DIR__ . '/../../Pattern/Dto/Mock/Matricula.php';

use Commons\Pattern\Dto\Aluno, Commons\Util\Reflect\ReflectionUtil;

class ReflectionUtilTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Método responsável por testar a criação de um Dto complexo
     * Obs: DeepPropertyExtractor converter todos objetos em arrays.
     */
    public function testDeepPropertyExtractor()
    {
        $aluno = $this->criarAluno();

        // Seguro de que o objeto é cíclico extrair sua versão profunda em array
        $arr = ReflectionUtil::deepPropertyExtractor($aluno);
        self::assertEquals($arr['nome'], $arr['matricula']['aluno']['nome']);
        self::assertEquals($arr['escola']['nome'], $arr['matricula']['aluno']['escola']['nome']);
    }

    /**
     * Método responsável por testar a criação de um Dto complexo.
     * Obs: PropertyExtractor não converte em array tipos complexos.
     */
    public function testPropertyExtractor()
    {
        $aluno = $this->criarAluno();

        // Seguro de que o objeto é cíclico extrair sua versão profunda em array
        $arr = ReflectionUtil::propertyExtractor($aluno);
        self::assertEquals($arr['nome'], $arr['matricula']->getAluno()->getNome());
        self::assertEquals($arr['escola']->getNome(), $arr['matricula']->getAluno()
            ->getEscola()
            ->getNome());
    }

    public function testCopyProperties_semMapeamentoEntreStdClass()
    {
        $recebedor = new \stdClass();
        $recebedor->nome = 'Recebedor';
        $recebedor->propRecebedor = 'Não altera essa propriedade recebedor';

        $doador = new \stdClass();
        $doador->nome = 'Doador';
        $doador->propDoador = 'Não altera essa propriedade doador';

        ReflectionUtil::copyProperties($recebedor, $doador);
        // verifica que o doador não foi modificado
        self::assertEquals('Doador', $doador->nome);
        self::assertEquals('Não altera essa propriedade doador', $doador->propDoador);

        // verifica que o recebedor recebeu o nome do doador
        self::assertEquals('Doador', $recebedor->nome);
        self::assertEquals('Não altera essa propriedade recebedor', $recebedor->propRecebedor);
    }

    public function testCopyProperties_semMapeamentoEntreStdClassEObjeto()
    {
        $alunoDados = new \stdClass();
        $alunoDados->cpf = null;
        $alunoDados->nome = null;

        $aluno = new Aluno();
        $aluno->setId(1000);
        $aluno->setCpf('99999999999');
        $aluno->setNome('Fulano');

        ReflectionUtil::copyProperties($alunoDados, $aluno);
        // verifica que o $aluno não foi modificado
        self::assertEquals(1000, $aluno->getId());
        self::assertEquals('99999999999', $aluno->getCpf());
        self::assertEquals('Fulano', $aluno->getNome());

        // verifica que o $alunoDados recebeu os dados
        self::assertEquals('99999999999', $alunoDados->cpf);
        self::assertEquals('Fulano', $alunoDados->nome);
    }

    public function testCopyProperties_semMapeamentoEntreObjetoEStdClass()
    {
        $alunoDados = new \stdClass();
        $alunoDados->cpf = '88888888888';
        $alunoDados->nome = 'Beltrano';

        $aluno = new Aluno();
        $aluno->setId(1001);
        $aluno->setCpf('99999999999');
        $aluno->setNome('Fulano');

        ReflectionUtil::copyProperties($aluno, $alunoDados);
        // verifica que o $alunoDados não foi modificado
        self::assertEquals('88888888888', $alunoDados->cpf);
        self::assertEquals('Beltrano', $alunoDados->nome);

        // verifica que o $aluno recebeu os dados
        self::assertEquals(1001, $aluno->getId());
        self::assertEquals('88888888888', $aluno->getCpf());
        self::assertEquals('Beltrano', $aluno->getNome());
    }

    public function testCopyProperties_semMapeamentoEntreObjetoEObjeto()
    {
        $aluno1 = new Aluno();
        $aluno1->setId(1001);
        $aluno1->setCpf('99999999999');
        $aluno1->setNome('Fulano');

        $aluno2 = new Aluno();
        $aluno2->setId(1002);
        $aluno2->setCpf('88888888888');
        $aluno2->setNome('Beltrano');

        ReflectionUtil::copyProperties($aluno1, $aluno2);
        // verifica que o $aluno2 não foi modificado
        self::assertEquals(1002, $aluno2->getId());
        self::assertEquals('88888888888', $aluno2->getCpf());
        self::assertEquals('Beltrano', $aluno2->getNome());

        // verifica que o $aluno1 recebeu os dados
        self::assertEquals(1002, $aluno1->getId());
        self::assertEquals('88888888888', $aluno1->getCpf());
        self::assertEquals('Beltrano', $aluno1->getNome());
    }

    public function testCopyProperties_ObjetoEObjetoMapeado()
    {
        $alunoDados = new \stdClass();
        $alunoDados->cadastroPessoaFisica = null;
        $alunoDados->nomeDoAluno = null;

        $aluno = new Aluno();
        $aluno->setId(1000);
        $aluno->setCpf('99999999999');
        $aluno->setNome('Fulano');

        ReflectionUtil::copyProperties($alunoDados, $aluno, array(
            get_class($alunoDados) => array(
                'cadastroPessoaFisica' => 'cpf',
                'nomeDoAluno' => 'nome'
            )
        ));

        // verifica que o $aluno não foi modificado
        self::assertEquals(1000, $aluno->getId());
        self::assertEquals('99999999999', $aluno->getCpf());
        self::assertEquals('Fulano', $aluno->getNome());

        // verifica que o $alunoDados recebeu os dados
        self::assertEquals('99999999999', $alunoDados->cadastroPessoaFisica);
        self::assertEquals('Fulano', $alunoDados->nomeDoAluno);
    }

    public function testCopyProperties_deepPropertiesCopy()
    {
        $aluno1 = $this->criarAluno();
        $aluno2 = $this->criarAluno();

        // modificar algumas coisas em $aluno2
        $aluno2->setId(2);
        $aluno2->setCpf('88888888888');
        $aluno2->setNome('Sicrano');
        $aluno2->setEscola(null);
        $aluno2->getMatricula()->setId(2);
        $aluno2->getMatricula()->setDataEntrada('11/11/1111');

        // garante que $aluno1 não foi modificado por referência
        self::assertEquals(1, $aluno1->getId());
        self::assertEquals('Fulano', $aluno1->getNome());
        self::assertEquals(99999999999, $aluno1->getCpf());
        self::assertEquals(1, $aluno1->getEscola()->getId());
        self::assertEquals('escolinha@mec.gov.br', $aluno1->getEscola()->getEmail());
        self::assertEquals('Escolinha Trololo', $aluno1->getEscola()->getNome());
        self::assertEquals('Ed. Trololo, Rua Bla, Lote. 0, Trololo City', $aluno1->getEscola()->getEndereco());
        self::assertEquals('1234-4321', $aluno1->getEscola()->getNumeroTelefone());
        self::assertEquals(1, $aluno1->getMatricula()->getId());
        self::assertEquals('01/08/2012', $aluno1->getMatricula()->getDataEntrada());

        // copia as propriedades de $aluno2 para $aluno1
        ReflectionUtil::copyProperties($aluno1, $aluno2);

        // verifica que o $aluno1 foi modificado
        self::assertEquals(2, $aluno1->getId());
        self::assertEquals('Sicrano', $aluno1->getNome());
        self::assertEquals(88888888888, $aluno1->getCpf());
        self::assertNull($aluno1->getEscola());
        self::assertEquals(2, $aluno1->getMatricula()->getId());
        self::assertEquals('11/11/1111', $aluno1->getMatricula()->getDataEntrada());

        // verifica que as propriedades-objeto são diferentes
        self::assertNotEquals(\spl_object_hash($aluno1), \spl_object_hash($aluno2));
        self::assertNotEquals(\spl_object_hash($aluno1->getMatricula()), \spl_object_hash($aluno2->getMatricula()));
    }

    public static function testArrayToObject()
    {
        $dados = array(
            'id' => 1,
            'ob1' => array(
                'id' => 2,
                'sob1' => array(
                    'id' => 3
                )
            )
        );
        $object = ReflectionUtil::arrayToObject($dados);
        self::assertInstanceOf('\stdClass', $object->ob1);
        self::assertInstanceOf('\stdClass', $object->ob1->sob1);
    }

    /**
     *
     * @return \Commons\Pattern\Dto\Aluno
     */
    public function criarAluno()
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

        return $aluno;
    }

    public function testGetArrayReference_referencia()
    {
        $var = 'teste';
        // Passagem por referência
        $array = array(&$var);
        $ref   = ReflectionUtil::getArrayReference($array);
        $ref[0] = $ref[0].' novo';
        $this->assertEquals($ref[0], $var);
    }

    public function testGetArrayReference_valor()
    {
        $var = 'teste';
        // Passagem por valor
        $array = array($var);
        $ref   = ReflectionUtil::getArrayReference($array);
        $ref[0] = $ref[0].' novo';
        $this->assertNotEquals($ref[0], $var);
    }
}
