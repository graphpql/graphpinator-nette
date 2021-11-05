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

    public function get(string $key, mixed $default = null) : mixed
    {
        return $this->cache->load($key, static function () use ($default) {
            return $default;
        });
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null) : bool
    {
        $options = [];
        
        if (\is_int($ttl)) {
            $options[\Nette\Caching\Cache::EXPIRATION] = (new \Nette\Utils\DateTime())->getTimestamp() + $ttl;
        }

        $this->cache->save($key, $value, $options);

        return true;
    }

    public function delete(string $key) : bool
    {
    }

    public function clear() : bool
    {
    }

    public function getMultiple(iterable $keys, mixed $default = null) : iterable
    {
    }

    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null) : bool
    {
    }

    public function deleteMultiple(iterable $keys) : bool
    {
    }

    public function has(string $key) : bool
    {
    }
}
