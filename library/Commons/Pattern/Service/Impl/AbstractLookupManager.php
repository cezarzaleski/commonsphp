<?php

namespace Commons\Pattern\Service\Impl;

use Commons\Pattern\Di\LookupManager;
use Commons\Pattern\Service\Service;

/**
 * Classe padrão para recuperação de serviços através de injeção de dependência.
 */
abstract class AbstractLookupManager implements LookupManager
{
    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Di\LookupManager::get()
     */
    public function get($name, array $params = array())
    {
        $result = $this->realGet($name, $params);
        if ($result && !($result instanceof Service)) {
            throw new \UnexpectedValueException(
                "Instância do serviço $name não é do tipo \Commons\Pattern\Service\Service."
                );
        }
        return $result;
    }

    /**
     * Retorna a instância de um objeto nomeado.
     *
     * @param string $name Nome do objeto
     * @param array $params Parâmetros para criação do objeto.
     */
    abstract protected function realGet($name, array $params = array());
}
