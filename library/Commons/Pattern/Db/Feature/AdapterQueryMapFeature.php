<?php

namespace Commons\Pattern\Db\Feature;

use Commons\Pattern\Plugin\Pluggable;
use Commons\Pattern\Plugin\Dispatcher;
use Commons\Pattern\Plugin\PluginInterceptor;
use Commons\Pattern\Db\Plugin\QueryMapPlugin;
use Commons\Pattern\Cache\Plugin\StandardCachePlugin;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSetInterface;
use Commons\Pattern\Cache\Cache;

/**
 * Característica de QueryMap para Adapter Zend.
 */
class AdapterQueryMapFeature implements Pluggable
{
    use \Commons\Pattern\Plugin\TPluggableHandler;
    use \Commons\Pattern\Cache\TCacheableHandler;

    /**
     * @var \Zend\Db\Adapter\Adapter Adaptador Zend.
     */
    private $adapter;

    /**
     * @var string Diretório do mapa de queries.
     */
    private $queryMapDirectory;

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @param \Commons\Pattern\Cache\Cache $cache
     * @param string $queryMapDirectory
     */
    public function __construct(Adapter $adapter, Cache $cache, $queryMapDirectory)
    {
        $this->adapter = $adapter;
        $this->queryMapDirectory = $queryMapDirectory;
        $this->setCache($cache);
    }

    /**
     * @param Dispatcher $dispatcher
     */
    protected function registerPlugins(Dispatcher $dispatcher)
    {
        $eligibleOperations = '/\bquery\b|\bcreateStatement\b/';
        $cache = new StandardCachePlugin($eligibleOperations, $this->getCache());
        $queryMap = new QueryMapPlugin($eligibleOperations, $this->queryMapDirectory);
        $dispatcher->addPlugin('cache', $cache);
        $dispatcher->addPlugin('query_map', $queryMap);
    }

    /**
     * versão query() para utilização de interceptadores de cache e querymap.
     *
     * @param string $sql
     * @param string|array|ParameterContainer $parametersOrQueryMode
     * @throws Exception\InvalidArgumentException
     * @return \Zend\Db\Adapter\Driver\StatementInterface|\Zend\Db\ResultSet\ResultSet
     */
    public function query(
        $sql,
        $parametersOrQueryMode = Adapter::QUERY_MODE_PREPARE,
        ResultSetInterface $resultPrototype = null
    ) {
        return PluginInterceptor::intercept(
            $this,
            $this->adapter,
            __FUNCTION__,
            array(
                $sql,
                $parametersOrQueryMode,
                $resultPrototype
            )
        );
    }

    /**
     * Cria statement com utilização de interceptadores de cache e querymap.
     *
     * @param  string $initialSql
     * @param  ParameterContainer $initialParameters
     * @return \Zend\Db\Adapter\Driver\StatementInterface
     */
    public function createStatement($initialSql = null, $initialParameters = null)
    {
        return PluginInterceptor::intercept(
            $this,
            $this->adapter,
            __FUNCTION__,
            array(
                $initialSql,
                $initialParameters
            )
        );
    }
}
