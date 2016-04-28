<?php

namespace Commons\Pattern\Di;

/**
 * Interface responsável por localizar um objeto e retornar sua instância.
 */
interface LookupManager
{
    /**
     * Retorna a instância de um serviço.
     *
     * @param  string      $name   Nome da classe ou do serviço
     * @param  null|array  $params Parâmetros utilizados quando instanciada a instância representada por $name
     * @return \Commons\Pattern\Service\Service|null
     * @throws \UnexpectedValueException quando a instância recuperada não é do tipo esperado.
     */
    public function get($name, array $params = array());
}
