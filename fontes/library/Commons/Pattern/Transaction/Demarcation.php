<?php
namespace Commons\Pattern\Transaction;

/**
 * Define uma operação de demarcação de transação.
 */
interface Demarcation
{
    /**
     * Demarca uma transação.
     *
     *  A demarcação deve garantir que ao final da execução do callback a transação tenha sido
     *  finalizada com commit ou rollback.
     *
     * @param callback $callback
     * @param array $args
     */
    public function demarcate($callback, array $args = array());
}
