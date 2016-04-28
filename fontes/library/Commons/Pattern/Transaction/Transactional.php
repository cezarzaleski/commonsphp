<?php
namespace Commons\Pattern\Transaction;

/**
 * Define as operações de manutenção de um escopo transacional.
 */
interface Transactional
{
    /**
     * Inicializa a transação.
     */
    public function beginTransaction();

    /**
     * Realiza a operação de rollback (desfazer operações).
     */
    public function rollback();

    /**
     * Realiza a operação de commit (confirmar operações).
     */
    public function commit();

    /**
     * Realiza o fechamento da estratégia transacional.
     */
    public function close();
}
