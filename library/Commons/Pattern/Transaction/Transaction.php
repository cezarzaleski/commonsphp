<?php

namespace Commons\Pattern\Transaction;

use Commons\Pattern\Transaction\Strategy\TransactionStrategy;

/**
 * Responsável por gerenciar o uso de transações.
 * Padroniza a utilização dos blocos transacionais:
 * try {
 *     beginTransaction;
 *     // operações transacionadas
 *     commit;
 * } catch(\Exception $e) {
 *     rollback;
 * }
 *
 * Após registrados as estratégias transacionais não se pode
 * usar os métodos das mesmas e sim a versão Transaction::* como no exemplo:
 *
 * try {
 *     Transaction::beginTransaction(nome da estratégia);
 *     // operações transacionadas
 *     Transaction::commit();
 * } catch(\Exception $e) {
 *     Transaction::rollback();
 * }
 *
 * Isso permite fazer aninhamento de transações de diversos bancos ou objetos transacionais
 * de tal modo que todos só irão realizar o commit quando todas as operações transacionadas
 * estiverem corretas. E realizar o rollback em todas as estratégias quando houve erro em
 * algum lugar.
 *
 * O mesmo bloco transacional acima pode ser substituído pela função de demarcação:
 *
 * Transaction::demarcate('alunoDb', function ($strategy) use($bindAluno)
 * {
 *     return $strategy->getAssigner()->query('select * from aluno where cod_aluno = :codAluno', $bindAluno);
 * });
 *
 * @category Commons
 * @package Commons\Pattern\Transaction
 */
final class Transaction
{

    /**
     * Mapa com as referências para @see Commons\Pattern\Transaction\TransactionStrategy.
     *
     * @var Commons\Pattern\Transaction\TransactionStrategy[]
     */
    private static $persistenceStrategies = array();

    /**
     * Lista que ordena as chamadas de transação dos @see Commons\Pattern\Transaction\TransactionStrategy.
     *
     * @var string[]
     */
    public static $order = array();

    /**
     * Lista de transações marcadas para rollback quando existem exceções não
     * tratadas no código.
     *
     * @var string[]
     */
    private static $rollbackMark = array();

    /**
     * Contador da transação, indica os níveis em que poderá ser feito a
     * inicialização e finalização da transação.
     *
     * @var integer
     */
    private static $counter = 0;

    /**
     * Indica o escopo atual da transação.
     *
     * @var string
     */
    private static $currentScope = null;

    /**
     * Indica o escopo passado.
     *
     * @var string
     */
    private static $lastScope = null;

    /**
     * (re)/Inicializa variáveis estáticas do Transaction.
     * Não recomendado utilizar essa função inadvertidamente,
     * pois poderá causar inconsistências.
     */
    public static function initialize()
    {
        static::$persistenceStrategies = array();
        static::$order = array();
        static::$rollbackMark = array();
        static::$counter = 0;
        static::$currentScope = null;
        static::$lastScope = null;
    }

    /**
     * Responsável por retornar a estratégia transacional cadastrada.
     *
     * @param string $name
     *            caso não seja inserido o nome, retorna a instância da
     *            estratégia transacional do escopo de transação corrente.
     * @return \Commons\Pattern\Transaction\TransactionStrategy
     */
    public static function getStrategy($name = true)
    {
        // se não for definido nenhum nome para estratégia transacional recupera o nome do escopo corrente.
        if ($name === true) {
            // se o escopo for null, apontará para estratégia transacaional default.
            $name = static::$currentScope;
        }

        if (! isset(static::$persistenceStrategies[$name])) {
            throw new TransactionException("Não existe estratégia transacional registrada.");
        }

        return static::$persistenceStrategies[$name];
    }

    /**
     * Responsável por verificar se existe uma transação ativa para uma estratégia transacional,
     * permitindo avaliar se o uso da estratégia é adequada no bloco transacional,
     * principalmente para execuções de operações como INSERT, UPDATE e DELETE.
     *
     * @param mixed $strategy
     *            Nome da estratégia de persistência.
     * @return boolean true para transação ativa, false para transação não iniciada
     *         ou sem estratégia transacional.
     */
    public static function verifyActiveTransaction($strategy)
    {
        $strategyName = $strategy;
        if ($strategy instanceof \Commons\Pattern\Transaction\Strategy\TransactionStrategy) {
            $strategyName = $strategy->getName();
        }

        return false !== $strategyName && in_array($strategyName, static::$order);
    }


    /**
     * Resolve a ativação do escopo transacional.
     *
     * @param \Commons\Pattern\Transaction\Strategy\TransactionStrategy $strategy
     */
    public static function resolveTransactionActivation($strategy)
    {
        if (! Transaction::isRegistered($strategy->getName())) {
            Transaction::unregisterStrategy($strategy->getName());
            Transaction::registerStrategy($strategy);
        }
    }

    /**
     * Registra uma estratégia transacional.
     *
     * @param Commons\Pattern\Transaction\Strategy\TransactionStrategy $strategy.
     * @throws Commons\Pattern\Transaction\TransactionException caso $strategy seja nulo.
     * @return void
     */
    public static function registerStrategy(TransactionStrategy $strategy)
    {
        // mesma estratégia com outro nome.
        $strategyName = array_search($strategy, static::$persistenceStrategies, true);
        if (false !== $strategyName && $strategyName !== $strategy->getName()) {
            throw new TransactionException("Não é permitido registrar mesma estratégia com nome distinto.");
        }

        // nome já utilizado por outra estratégia.
        if (isset(static::$persistenceStrategies[$strategy->getName()]) &&
            static::$persistenceStrategies[$strategy->getName()] !== $strategy) {
            throw new TransactionException("Não é permitido registrar estratégias diferentes com o mesmo nome.");
        }

        // mesmo delegador em estratégias diferentes.
        foreach (static::$persistenceStrategies as $name => $item) {
            if ($item->getAssigner() === $strategy->getAssigner()) {
                throw new TransactionException(
                    "Não é permitido registrar estratégias diferentes com o mesmo delegador. ".
                    "Estratégia repetida: $name"
                );
            }
        }

        static::$persistenceStrategies[$strategy->getName()] = $strategy;
    }

    /**
     * Remove a estratégia transacional da lista de estratégias.
     *
     * @param mixed $strategy
     * @throws Commons\Pattern\Transaction\TransactionException caso exista uma transação ativa.
     * @return Commons\Pattern\Transaction\Strategy\TransactionStrategy estratégia removida.
     */
    public static function unregisterStrategy($strategy)
    {
        $unregisteredStrategy = null;
        $strategyName = $strategy;
        if ($strategy instanceof \Commons\Pattern\Transaction\Strategy\TransactionStrategy) {
            $strategyName = $strategy->getName();
        }

        if (false !== $strategyName && array_key_exists($strategyName, static::$persistenceStrategies)) {
            if (static::verifyActiveTransaction($strategyName)) {
                throw new TransactionException(
                    "Não é possível remover uma estrátegia transacional enquanto existir uma transação ativa."
                );
            } else {
                $unregisteredStrategy = static::$persistenceStrategies[$strategyName];
                unset(static::$persistenceStrategies[$strategyName]);
            }
        }
        return $unregisteredStrategy;
    }

    /**
     * Verifica se a estratégia está registrada.
     *
     * @param mixed $strategy
     * @return boolean
     */
    public static function isRegistered($strategy)
    {
        $strategyName = $strategy;
        if ($strategy instanceof \Commons\Pattern\Transaction\Strategy\TransactionStrategy) {
            return false !== array_search($strategy, static::$persistenceStrategies, true);
        }
        return array_key_exists($strategyName, static::$persistenceStrategies);
    }

    /**
     * Inicia uma transação nomeada (de uma estratégia transacional específica).
     *
     * @param string $name
     *            Nome da estratégia transacional que se deseja utilizar.
     * @return void
     */
    public static function beginTransaction($name)
    {
        static::$currentScope = $name;
        if (static::$counter == 0 || ! in_array($name, static::$order)) {
            static::getStrategy($name)->beginTransaction();
            static::$order[] = $name;
        }
        static::$counter += 1;
    }

    /**
     * Finaliza a transação com commit.
     *
     * @return void
     */
    public static function commit()
    {
        static::finalization(false);
    }

    /**
     * Finaliza a transação com rollback.
     *
     * @return void
     */
    public static function rollback()
    {
        static::$rollbackMark[] = static::$currentScope;
        static::finalization(true);
    }

    /**
     * Finaliza a transação.
     *
     * @param boolean $rollback
     *            true para rollback, false para commit.
     * @return void
     */
    private static function finalization($rollback)
    {
        static::$counter -= 1;
        if (static::$counter == 0) {
            for ($i = sizeof(static::$order) - 1; $i >= 0; $i --) {
                if (!static::rollbackOrCommit($rollback, $i)) {
                    continue;
                }
            }
            static::$order = array();
            static::$rollbackMark = array();
        } elseif (static::$counter < 0) {
            static::$counter = 0;
            throw new TransactionException("Não pode finalizar uma transação não inicializada.");
        }
        static::$currentScope = static::$lastScope;
    }

    /**
     * Finaliza transação por Rollback (true) ou Commit (false)
     * @param boolean $rollback quando true, commit quando false.
     * @param int $position Ordem da transação a ser executado rollback ou commit.
     * @return true se ocorreu tudo bem, false caso contrário.
     * @throws Exception
     */
    private static function rollbackOrCommit($rollback, $position)
    {
        $return = true;
        if ($rollback) {
            try {
                $strategy = static::$persistenceStrategies[static::$order[$position]];
                $strategy->rollback();
            } catch (\Exception $e) {
                $return = false;
            }
        } else {
            try {
                if (in_array(static::$order[$position], static::$rollbackMark)) {
                    $strategy = static::$persistenceStrategies[static::$order[$position]];
                    $strategy->rollback();
                } else {
                    $strategy = static::$persistenceStrategies[static::$order[$position]];
                    $strategy->commit();
                }
            } catch (\Exception $e) {
                static::$counter += 1;
                throw $e;
            }
        }
        return $return;
    }


    /**
     * Fecha todas as estratégias de persistência.
     *
     * @param array $names
     *            Nomes das transações que serão fechadas ou
     *            null para fechamento de todas.
     * @return void
     */
    public static function closeStrategies(array $names = array())
    {
        if (! is_null(static::$persistenceStrategies)) {
            foreach (static::$persistenceStrategies as $strategyName => $strategy) {
                if (empty($names) || in_array($strategyName, $names)) {
                    $strategy->close();
                }
            }
        }
    }


    /**
     * Demarca um bloco transacional.
     *
     * O callback deverá ser do tipo:
     * function (TransactionStrategy $strategy, $param1, $param2,...) use ($externo1, $externo2){
     * ...
     * }
     *
     * Ou seja os argumentos passados em $args serão combinados com o objeto de estratégia transacional
     * gerando a lista de parâmetros do callback.
     *
     * @param string $name
     *            Nome da estratégia transacional a ser utilizada.
     * @param function $callback
     * @param array $args
     * @throws Commons\Pattern\Transaction\TransactionException caso $callback seja nulo
     * ou ocorra algum erro na transação.
     * @throws Exception relança a exceção caso ocorra rollback.
     * @return mixed resultado de $callback.
     */
    public static function demarcate($name, $callback, array $args = array())
    {
        if (! is_callable($callback)) {
            throw new TransactionException("Callback ou closure deve ser definida.");
        }
        $result = null;
        try {
            static::beginTransaction($name);
            $result = \call_user_func_array($callback, array_merge(array(static::getStrategy($name)), $args));
            static::commit();
        } catch (\Exception $e) {
            static::rollback();
            throw $e;
        }
        return $result;
    }
}
