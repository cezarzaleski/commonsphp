<?php

namespace Commons\Pattern\Plugin;

use Commons\Util\Reflect\ReflectionUtil;

/**
 * Interceptador de Plugins.
 */
class PluginInterceptor
{
    /**
     * Método responsável por interceptar uma operação.
     *
     * @param Pluggable $owner   Objeto que possui os plugins a serem aplicados.
     * @param object $owner      Dono do método.
     * @param string $operation  Nome do método.
     * @param array $params      Parâmetros do método.
     * @return mixed             Resultado após execução do método e plugins.
     */
    public static function intercept(Pluggable $plugins, $owner, $operation, array $params)
    {
        // prepara contexto
        $params = ReflectionUtil::getArrayReference($params);
        $context = new Context($plugins, $owner, $operation, $params);
        // realiza operação
        $callback = array(
            $owner,
            $operation
        );
        $pluginDispatcher = $plugins->getPluginDispatcher();
        $pluginDispatcher->preDispatch($context);

        if (! $context->isLocked()) {
            $context->setReturn(call_user_func_array($callback, $context->getParams()));
        }
        $pluginDispatcher->postDispatch($context);

        return $context->getReturn();
    }
}
