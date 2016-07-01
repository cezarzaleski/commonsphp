<?php

namespace Commons\Pattern\Validator;

/**
 * Interface para definir se um objeto tem a qualidade de validar parâmetros.
 *
 * Similar à definida em Zend\Validator\ValidatorInterface
 */
interface Validatable
{
    /**
     * Retorna true apenas se o valor $value atender os critérios estipulados.
     * Caso contrário, retornará false e será preenchido um objeto com as mensagens de erro.
     *
     * As mensagens podem ser recuperadas pelo método getMessages() que retorna um array.
     *
     * @param  mixed $value
     * @return boolean
     * @throws \RuntimeException If validation of $value is impossible
     */
    public function isValid($value);

    /**
     * Retorna as mensagens que explicam o erro ocorrido em isValid().
     *
     * As chaves do array deverão ser identificadores da mensagem e o valor strings para leitura humana.
     *
     * Caso isValid() nunca seja chamado ou se a ultima avaliação de isValid() for true esse método
     * deverá retornar um array vazio.
     *
     * @return array
     */
    public function getMessages();
}

