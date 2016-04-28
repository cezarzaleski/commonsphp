<?php

namespace Commons\Pattern\Db\Plugin;

use Commons\Pattern\Plugin\Context;
use Commons\Pattern\Plugin\Impl\Selector;

/**
 * @category Commons
 * @package Commons\Pattern\Transaction
 */
class QueryMapPlugin extends Selector
{

    /**
     *
     * @var string
     */
    protected $id = 'query_map_plugin';

    /**
     *
     * @var \stdClass[]
     */
    protected $map = null;

    /**
     *
     * @var string
     */
    protected $current;

    private $queryMapDirectory;

    public function __construct($regex, $queryMapDirectory = null)
    {
        parent::__construct($regex);
        $this->queryMapDirectory = $queryMapDirectory;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Hive\Plugin\Plugin::preDispatch()
     */
    public function preDispatch(Context $context)
    {
        $this->current = null;
        if (parent::preDispatch($context)) {
            $this->loadQueryMap($this->getQueryMapDirectory());
            $map = $this->map;
            $params = &$context->getParams();
            $sql = reset($params);

            if (isset($map->{$sql})) {
                $this->current = $sql;
                $meta = $map->{$sql};
                // verifica tempo de cache
                $this->verifyCacheTtl($map);
                // altera sql
                $params[0] = $meta->sql;
            }
        }
    }

    /**
     *
     * @param \stdClass $map
     * @return void
     */
    private function verifyCacheTtl($map)
    {
        if (isset($map->ttlmin) || isset($map->ttlmax)) {
            $cachePlugin = $this->dispatcher->getPlugin('cache');
            if ($cachePlugin && $cachePlugin->isEnable()) {
                $ttl = $cachePlugin->getTtl();
                if (isset($map->ttlmin) && $ttl < $map->ttlmin) {
                    $ttl = $map->ttlmin;
                }
                if (isset($map->ttlmax) && $ttl > $map->ttlmax) {
                    $ttl = $map->ttlmax;
                }
                $cachePlugin->enable($ttl);
            }
        }
    }

    /**
     *
     * @return \stdClass[]
     */
    protected function loadQueryMap($directory)
    {
        if (null != $this->map) {
            return;
        }

        // captura do cache
        $cache = $this->dispatcher->getPlugin('cache')->getCache();
        if ($cache && $cache->contains($this->id)) {
            $this->map = $cache->get($this->id);
            return;
        }
        // lê do disco
        $this->map = $this->loadMapDirectory($directory);
        if ($cache) {
            $cache->set($this->id, $this->map);
        }
    }

    /**
     *
     * @param string $directory
     * @return \stdClass
     */
    protected function loadMapDirectory($directory)
    {
        $buffer = new \stdClass();
        if (is_dir($directory)) {
            $dh = \opendir($directory);
            if ($dh) {
                while (($file = readdir($dh)) !== false) {
                    $path = $directory . '/' . $file;
                    $extension = substr($file, strrpos($file, '.') + 1);
                    if ('file' != filetype($path) || 'xml' !== $extension) {
                        continue;
                    }
                    // parsear arquivos
                    $currentBuffer = $this->loadMapFile($path);
                    $buffer = (object) array_merge((array) $buffer, (array) $currentBuffer);
                }
                closedir($dh);
            }
        } else {
            if (null != $directory && ! empty($directory)) {
                throw new \InvalidArgumentException('Diretório de mapas de queries inválido.');
            }
        }
        return $buffer;
    }

    /**
     *
     * @param string $xmlPath
     * @return \stdClass
     */
    protected function loadMapFile($xmlPath)
    {
        $buffer = new \stdClass();
        $xmlParser = simplexml_load_file($xmlPath, 'SimpleXMLElement', LIBXML_NOCDATA);
        foreach ($xmlParser as $value) {
            $sql = trim((string) $value);
            if (empty($sql) || empty($value->attributes()->id)) {
                continue;
            }

            $attr = $value->attributes();
            $query = new \stdClass();

            $id = null;
            if (null !== $attr->id) {
                $id = (string) $attr->id;
            }

            if (null !== $attr->ttlmin) {
                $query->ttlmin = (string) $attr->ttlmin;
            }

            if (null !== $attr->ttlmax) {
                $query->ttlmax = (string) $attr->ttlmax;
            }

            if (null !== $attr->lob) {
                $query->lob = (string) $attr->lob;
            }

            $query->sql = $sql;

            $buffer->{$id} = $query;
        }

        return $buffer;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Hive\Plugin\Plugin::postDispatch()
     */
    public function postDispatch(Context $context)
    {
        if (parent::postDispatch($context) && isset($this->map->{$this->current})) {
            $meta = $this->map->{$this->current};
            if (isset($meta->lob) && $meta->lob === "true") {
                $return = $context->getReturn();
                $this->parseLob($return);
                $context->setReturn($return);
            }
        }
    }

    /**
     * Ajuste no tratamento de Lobs
     * @param mixed $data
     * @return void
     */
    protected function parseLob(&$data)
    {
        $type = 'OCI-Lob';
        if (\is_resource($data) && 'stream' == \get_resource_type($data)) {
            $data = \stream_get_contents($data);
        } elseif ($data instanceof $type) {
            $size = $data->size();
            $data = $size > 0 ? $data->read($data->size()) : null;
        } elseif (is_array($data) || is_object($data)) {
            foreach ($data as &$sub) {
                $this->parseLob($sub);
            }
        }
    }

    /**
     *
     * @param string $queryMapDirectory
     * @return void
     */
    public function setQueryMapDirectory($queryMapDirectory)
    {
        $this->queryMapDirectory = $queryMapDirectory;
    }

    /**
     *
     * @return string
     */
    public function getQueryMapDirectory()
    {
        return $this->queryMapDirectory;
    }
}
