<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.1.2
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.1.0
 */

namespace Ascmvc\Session\Cache;

use Ascmvc\Session\Config;
use Doctrine\Common\Cache\ClearableCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Cache\XcacheCache;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class DoctrineCacheItemPool
 *
 * @package Ascmvc\Session\Cache
 */
class DoctrineCacheItemPool implements CacheItemPoolInterface
{
    /**
     * Contains an instance of the storage driver.
     *
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $driver;

    /**
     * Contains an array of deferred cache items.
     *
     * @var array CacheItemInterface
     */
    protected $deferred = [];

    /**
     * DoctrineCacheItemPool constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $driverName = $config->get('doctrine_cache_driver');

        // @codeCoverageIgnoreStart
        if ($driverName === FilesystemCache::class) {
            $cacheDirectory = $config->get('doctrine_filesystem_cache_directory');

            $this->driver = new $driverName($cacheDirectory);
        } elseif ($driverName === XcacheCache::class) {
            $this->driver = new $driverName();
        } else {
            $host = $config->get('doctrine_cache_server_params')['host'];

            $port = $config->get('doctrine_cache_server_params')['port'];

            if ($driverName === RedisCache::class) {
                $redis = new \Redis();

                $redis->connect($host, $port);

                $this->driver = new $driverName();

                $this->driver->setRedis($redis);
            } elseif ($driverName === MemcachedCache::class) {
                $memcached = new \Memcached();

                $memcached->addServer($host, $port);

                $this->driver = new $driverName();

                $this->driver->setMemcached($memcached);
            } elseif ($driverName === MemcacheCache::class) {
                $memcache = new \Memcache();

                $memcache->connect($host, $port);

                $this->driver = new $driverName();

                $this->driver->setMemcache($memcache);
            }
        }
        // @codeCoverageIgnoreEnd

        return;
    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item.
     *
     * @throws DoctrineInvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return CacheItemInterface
     *   The corresponding Cache Item.
     */
    public function getItem($key) : CacheItemInterface
    {
        if (!ctype_alnum($key)) {
            throw new DoctrineInvalidArgumentException($key);
        }

        $result = $this->driver->fetch($key);

        return new DoctrineCacheItem($key, $result);
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys
     *   An indexed array of keys of items to retrieve.
     *
     * @throws DoctrineInvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return array|\Traversable
     *   A traversable collection of Cache Items keyed by the cache keys of
     *   each item. A Cache item will be returned for each key, even if that
     *   key is not found. However, if no keys are specified then an empty
     *   traversable MUST be returned instead.
     */
    public function getItems(array $keys = [])
    {
        foreach ($keys as $key) {
            $result = $this->getItem($key);

            $collection[] = $result;
        }

        return $collection;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *   The key for which to check existence.
     *
     * @throws DoctrineInvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        if (!ctype_alnum($key)) {
            throw new DoctrineInvalidArgumentException($key);
        }

        return $this->driver->contains($key);
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        /**
         * Not all Doctrine Caches implement the ClearableCache interface.
         */
        if ($this->driver instanceof ClearableCache) {
            return $this->driver->deleteAll();
        }

        return false;
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     *   The key to delete.
     *
     * @throws DoctrineInvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        if (!ctype_alnum($key)) {
            throw new DoctrineInvalidArgumentException($key);
        }

        return $this->driver->delete($key);
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param string[] $keys
     *   An array of keys that should be removed from the pool.

     * @throws DoctrineInvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            $result = $this->deleteItem($key);

            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   True if the item was successfully persisted. False if there was an error.
     */
    public function save(CacheItemInterface $item)
    {
        return $this->driver->save(
            $item->getKey(),
            $item->get(),
            $item->getExpiration()
        );
    }

    /**
     * Sets a cache item to be committed later.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $key = $item->getKey();

        if (key_exists($key, $this->deferred)) {
            $result = $this->save($this->deferred[$key]);

            if (!$result) {
                return false;
            }
        }

        $this->deferred[$key] = $item;

        return true;
    }

    /**
     * Commits any deferred cache items.
     *
     * @return bool
     *   True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit()
    {
        if (empty($this->deferred)) {
            return true;
        } else {
            foreach ($this->deferred as $key => $item) {
                $result = $this->save($item);

                if (!$result) {
                    return false;
                }

                unset($this->deferred[$key]);
            }

            return true;
        }
    }
}
