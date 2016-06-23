<?php

namespace CommonsTest\Util\Test;

use Commons\Util\Test\GenericTestCase;
use Commons\Util\Test\ResultAsserterFactory;
use Commons\Util\Test\ReverseTestCaseGeneratorImpl;

/**
 * Classe ReverseTestCaseGeneratorImplTest.
 */
class ReverseTestCaseGeneratorImplTest extends GenericTestCase
{
    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        return 'Commons\Util\Test\ReverseTestCaseGeneratorImpl';
    }

    /**
     * Testa interface do método Main.
     *
     * @param mixed $expectedResult
     * @param array $params
     * @dataProvider publicInterfaceMain
     */
    public function testMain($expectedResult, $params)
    {
        $classDef = new ReverseTestCaseGeneratorImpl();

        @ob_start();
        $classDef->main($params[0], $params[1]);
        $result = @ob_get_contents();
        @ob_end_clean();
        self::assertEquals($expectedResult, $result);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método Main.
     */
    public function publicInterfaceMain()
    {
        $usage = <<<USAGE
********************************************************************************
** MEC - Gerador de classes de teste por interface publica                    **
********************************************************************************
Uso: php -f <dir>\ReverseTestCaseGenerator.php <bootstrap file> <source dir> <test dir>
Exemplo: Na raiz da aplicacao:
    vendor\bin\ReverseTestCaseGenerator vendor\autoload.php src tests
<bootstrap file>  Arquivo que carrega as classes.

<source dir>      Diretorio com os arquivos fontes que se deseja criar
                  testes unitarios para cada classe.

<test dir>        Diretorio aonde os arquivos de teste serao gerados.
                  Caso ja existam, nao serao sobrescritos.

Powered by: ZendFramework2

USAGE;

        return array(
//                    array('result', array(array('boot'), 1)), // testa a criação das pastas.
//                    array($usage, array(array(), 0)),
                    array($usage, array(array('boot'), 1)),
                    array($usage, array(array('boot', 'src'), 2)),
                    array('Arquivo bootstrap nao definidoAlgum dos diretorios informados nao existem', array(array('ReverseTestCaseGenerator','boot', 'src', 'test'), 3)),
                    array($usage, array(array(), null))
                );
    }

}
