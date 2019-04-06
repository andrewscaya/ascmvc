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

namespace Ascmvc\Session\Cache;

use Psr\Cache\CacheItemInterface;

class DoctrineCacheItem implements CacheItemInterface
{
    /**
     * Contains the result of the cache item lookup.
     *
     * @var bool
     */
    protected $hit = true;

    /**
     * Contains the item's key.
     *
     * @var string
     */
    protected $key = '';

    /**
     * Contains the value of the item from the cache associated with this object's key.
     *
     * @var array
     */
    protected $data;

    /**
     * Contains the item expiration time in seconds.
     *
     * @var int
     */
    protected $expiration = 0;

    /**
     * DoctrineCacheItem constructor.
     *
     * @param $key
     * @param $result
     */
    public function __construct($key, $data)
    {
        $this->key = $key;

        if ($data === false) {
            $this->hit = false;
        } else {
            $this->set($data);
        }
    }

    /**
     * Returns the key for the current cache item.
     *
     * The key is loaded by the Implementing Library, but should be available to
     * the higher level callers when needed.
     *
     * @return string
     *   The key string for this cache item.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Retrieves the value of the item from the cache associated with this object's key.
     *
     * The value returned must be identical to the value originally stored by set().
     *
     * If isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed
     *   The value corresponding to this cache item's key, or null if not found.
     */
    public function get()
    {
        return $this->data;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * Note: This method MUST NOT have a race condition between calling isHit()
     * and calling get().
     *
     * @return bool
     *   True if the request resulted in a cache hit. False otherwise.
     */
    public function isHit() {
        return $this->hit;
    }

    /**
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * @param mixed $value
     *   The serializable value to be stored.
     *
     * @return static
     *   The invoked object.
     */
    public function set($value)
    {
        $this->data = null;

        $this->data = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param \DateTimeInterface|null $expiration
     *   The point in time after which the item MUST be considered expired.
     *   If null is passed explicitly, a default value MAY be used. If none is set,
     *   the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     */
    public function expiresAt($expiration)
    {
        if ($expiration instanceof \DateTimeInterface) {
            $time = $expiration->diff(new \DateTime('NOW'));
            $this->expiration = $time->i * $time->s;
        } else {
            $this->expiration = 0;
        }

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval|null $time
     *   The period of time from the present after which the item MUST be considered
     *   expired. An integer parameter is understood to be the time in seconds until
     *   expiration. If null is passed explicitly, a default value MAY be used.
     *   If none is set, the value should be stored permanently or for as long as the
     *   implementation allows.
     *
     * @return static
     *   The called object.
     */
    public function expiresAfter($time)
    {
        if (is_int($time)) {
            $this->expiration = $time;
        }

        if ($time instanceof \DateInterval) {
            $this->expiration = $time->i * $time->s;
        } else {
            $this->expiration = 0;
        }

        return $this;
    }
}