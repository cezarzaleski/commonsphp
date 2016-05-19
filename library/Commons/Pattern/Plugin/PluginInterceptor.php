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
    public static function intercept(
        Pluggable $plugins,
        $owner,
        $operation,
        array $params,
        $operationType = Context::OP_METHOD
    ) {
        // prepara contexto
        $params = ReflectionUtil::getArrayReference($params);
        $context = new Context($plugins, $owner, $operation, $params, $operationType);
        // criar closure da operação
        $closure = function (Context $context) {
            $callback = array(
                $context->getOwner(),
                $context->getOperation()
            );
            return call_user_func_array($callback, $context->getParams());
        };

        return static::interceptThroughClosure($context, $closure);
    }

    /**
     * Método responsável por interceptar uma operação através de uma closure.
     *
     * @param Context $context   Contexto de execução da interceptação.
     * @param callback $closure  Callback que executará a operação a ser envolvida.
     */
    public static function interceptThroughClosure(Context $context, $closure)
    {
        $pluginDispatcher = $context->getPluggable()->getPluginDispatcher();

        try {
            $pluginDispatcher->preDispatch($context);
            if (! $context->isLocked()) {
                $context->setReturn($closure($context));
            }
            $pluginDispatcher->postDispatch($context);
        } catch (\Exception $e) {
            $context->setException($e);
            $pluginDispatcher->errorDispatch($context);
        } finally {
            $pluginDispatcher->finallyDispatch($context);
        }

        return $context->getReturn();
    }
}
