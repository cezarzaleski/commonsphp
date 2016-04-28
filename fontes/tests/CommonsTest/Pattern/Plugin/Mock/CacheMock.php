<?php
namespace CommonsTest\Pattern\Plugin\Mock;

use Commons\Pattern\Plugin\Impl\Cache;
use Zend\Cache\Storage\Adapter\FilesystemOptions;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Plugin\Serializer;
use Commons\Pattern\Cache\CacheZendAdapter;

class CacheMock extends Cache
{
    protected $prefix = 'cachemock_plugin';

    public function getCache()
    {
        if (! $this->cache) {
            $options = new FilesystemOptions();
            $options->setTtl(120);
            $options->setCacheDir(sys_get_temp_dir());
            $options->setNamespace('CacheMock');
            $zendStore = new Filesystem($options);
            $zendStore->addPlugin(new Serializer());
            $zendStore->clearByNamespace('CacheMock');
            $this->cache = new CacheZendAdapter($zendStore);
        }
        return $this->cache;
    }
}
