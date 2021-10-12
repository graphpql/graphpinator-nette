<?php

declare(strict_types = 1);

namespace Graphpinator\Nette;

final class NetteCache implements \Psr\SimpleCache\CacheInterface
{
    use \Nette\SmartObject;

    private \Nette\Caching\Cache $cache;

    public function __construct(\Nette\Caching\Storage $storage)
    {
        $this->cache = new \Nette\Caching\Cache($storage, 'persisted_queries');
    }

    public function get($key, $default = null)
    {
        return $this->cache->load($key, static function () use ($default) {
            return $default;
        });
    }

    public function set($key, $value, $ttl = null) : bool
    {
        $options = [];
        
        if (\is_int($ttl)) {
            $options[\Nette\Caching\Cache::EXPIRATION] = (new \Nette\Utils\DateTime())->getTimestamp() + $ttl;
        }

        $this->cache->save($key, $value, $options);

        return true;
    }

    public function delete($key) : void
    {
    }

    public function clear() : void
    {
    }

    public function getMultiple($keys, $default = null) : void
    {
    }

    public function setMultiple($values, $ttl = null) : void
    {
    }

    public function deleteMultiple($keys) : void
    {
    }

    public function has($key) : void
    {
    }
}
