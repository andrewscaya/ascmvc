<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.1.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.1.0
 */

namespace Ascmvc\Cache;

use Doctrine\Common\Cache\Cache;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

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

    public function __construct($driver, $params)
    {
        $this->driver = new $driver($params);
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
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return CacheItemInterface
     *   The corresponding Cache Item.
     */
    public function getItem($key) : CacheItemInterface
    {
        if(!ctype_alnum($key)) {
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
     * @throws InvalidArgumentException
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
        foreach($keys as $key) {
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
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        if(!ctype_alnum($key)) {
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
         * Will uphold the *Dependency inversion principle* (SOLID) -
         * depend upon abstractions rather than concretions.
         */
        return false;
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     *   The key to delete.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        if(!ctype_alnum($key)) {
            throw new DoctrineInvalidArgumentException($key);
        }

        return $this->driver->delete($key);
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param string[] $keys
     *   An array of keys that should be removed from the pool.

     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *   True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys){
        foreach($keys as $key) {
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
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *   The cache item to save.
     *
     * @return bool
     *   False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        if(key_exists($item->getKey(), $this->deferred)) {
            $result = $this->save($this->deferred[$item->getKey()]);

            if(!$result) {
                return false;
            }
        }

        $this->deferred[$item->getKey()] = $item;

        return true;

    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *   True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit()
    {
        if(empty($this->deferred)) {
            return true;
        } else {
            foreach($this->deferred as $key => $item) {
                $result = $this->save($item);

                if(!$result) {
                    return false;
                }
            }
        }
    }
}