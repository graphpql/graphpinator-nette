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

    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null) : bool
    {
        $options = [];

        if (\is_int($ttl)) {
            $options[\Nette\Caching\Cache::Expire] = (new \Nette\Utils\DateTime())->getTimestamp() + $ttl;
        }

        $this->cache->save($key, $value, $options);

        return true;
    }

    public function delete(string $key) : bool
    {
        return true;
    }

    public function clear() : bool
    {
        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null) : iterable
    {
        return [];
    }

    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null) : bool
    {
        return true;
    }

    public function deleteMultiple(iterable $keys) : bool
    {
        return true;
    }

    public function has(string $key) : bool
    {
        return false;
    }
}
