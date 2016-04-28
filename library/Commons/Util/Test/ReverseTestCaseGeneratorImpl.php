<?php

namespace Commons\Util\Test;

use Zend\Code\Scanner\DirectoryScanner;

final class ReverseTestCaseGeneratorImpl
{
    public function main(array $argv, $argc)
    {
        if ($argc < 3) {
            echo <<<USAGE
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
        } else {
            $bootstrap = $argv[1];
            $sources   = $argv[2];
            $tests     = $argv[3];

            if (!\file_exists($bootstrap)) {
                echo 'Arquivo bootstrap nao definido';
            } else {
                include $bootstrap;
            }
            if (!(\file_exists($sources) && \file_exists($tests))) {
                echo 'Algum dos diretorios informados nao existem';
                return -1;
            }
            $directoryScanner = new DirectoryScanner($sources);
            $classes = $directoryScanner->getClassNames();
            $this->generateTestClasses($classes, $tests);
        }
    }

    protected function generateTestClasses($classes, $testFolder)
    {
        foreach ($classes as $class) {
            $refClass = new \ReflectionClass($class);
            if ($refClass->isInstantiable()) {
                $firstDir = \explode('\\', $class)[0];
                $testClassName = $firstDir.'Test'. \substr($class, \strlen($firstDir));
                $testClassName .= 'Test';
                $testClassFileName = $testFolder.'\\'. $testClassName;
                if (!\file_exists(\dirname($testClassFileName)) && !\mkdir(\dirname($testClassFileName), 0777, true)) {
                    continue;
                }
                $newfile = $testClassFileName.'.php';
                if (!\file_exists($newfile)) {
                    $handle = \fopen($newfile, 'w+');
                    if ($handle) {
                        \fwrite($handle, $this->generateTestContent($class, $testClassName));
                        \fflush($handle);
                        \fclose($handle);
                    }
                }
            }
        }
    }

    protected function generateTestContent($class, $testClassName)
    {
        $namespace = \dirname($testClassName);
        $className = \str_replace($namespace.'\\', '', $testClassName);
        $methods   = $this->generateTestMethods($class);
        return <<<"CLASS"
<?php

namespace $namespace;

use Commons\Util\Test\GenericTestCase;
use Commons\Util\Test\ResultAsserterFactory;

/**
 * Classe $className.
 */
class $className extends GenericTestCase
{
    /**
     * Método responsável por criar a definição da classe.
     *
     * @return mixed class name or object instance.
     */
    public function createDefinition()
    {
        return '$class';
    }
$methods
}

CLASS;
    }

    protected function generateTestMethods($class)
    {
        $refClass = new \ReflectionClass($class);
        $methods  = $refClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        $methodStubs = '';
        foreach ($methods as $refMethod) {
            if (!$refMethod->isDestructor()) {
                $parameters = $this->methodParametersStub($refMethod->getNumberOfParameters());
                $methodStubs .= $this->methodStub($refMethod->getName(), $parameters);
            }
        }
        return $methodStubs;
    }

    protected function methodParametersStub($countParams)
    {
        $parametersStub = '';
        if ($countParams > 0) {
            $parametersStub = "array(\n";
            $cenarios = \pow(2, $countParams);
            for ($i = 0; $i < $cenarios; $i++) {
                $arraySeparator = ($i == $cenarios - 1 ? "" : ",");
                $elements = '';
                for ($j = 0; $j < $countParams; $j++) {
                    $limiar = \pow(2, $countParams - 1 - $j);
                    $elements .= ($j == 0 ? "" : ", ");
                    $elements .= ($i/$limiar) % 2 ? "null" : "'?'" ;
                }
                $parametersStub .= "                    array('result', array($elements)){$arraySeparator}\n";
            }
            $parametersStub .= "                )";
        } else {
            $parametersStub = "array( array( 'expectedResult' , null ) )";
        }
        return $parametersStub;
    }

    protected function methodStub($methodName, $parameters)
    {
        $methodNameUpper = \ucfirst($methodName);
        return <<<"METHOD"

    /**
     * Testa interface do método $methodNameUpper.
     *
     * @param mixed \$expectedResult
     * @param array \$params
     * @dataProvider publicInterface$methodNameUpper
     */
    public function test$methodNameUpper(\$expectedResult, \$params)
    {
        \$classDef = \$this->createDefinition();
        \$this->assertPublicInterface(\$classDef, '$methodName', \$expectedResult, \$params);
    }

    /**
     * Provedor de dados com a combinatória de testes para o método $methodNameUpper.
     */
    public function publicInterface$methodNameUpper()
    {
        return $parameters;
    }

METHOD;
    }
}
