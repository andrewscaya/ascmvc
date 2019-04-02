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
 *
 * @see       https://github.com/itxiao6/session for the canonical source repository
 * @copyright Copyright (c) 2018  戒尺 包描述
 * @license   https://opensource.org/licenses/MIT
 */

namespace Ascmvc\Session;

use Psr\Cache\CacheItemPoolInterface;

/**
 * Class Session
 *
 * @package Ascmvc\Session
 */
class Session
{
    /**
     * Contains a SessionManager instance.
     *
     * @var SessionManager
     */
    protected $sessionManager;

    /**
     * Contains the session Config object.
     *
     * @var Config
     */
    protected $config;

    /**
     * Contains the session Http object.
     *
     * @var Http|null
     */
    protected $http = null;

    /**
     * Contains the session id string.
     *
     * @var string|null
     */
    protected $sessionId = null;

    /**
     * Contains the session's data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Contains a PSR-6 CacheItemPoolInterface instance.
     *
     * @var CacheItemPoolInterface|null
     */
    protected $sessionCachePool = null;

    /**
     * Contains the session's cache item.
     *
     * @var CacheItemInterface|null
     */
    protected $sessionCacheItem = null;

    /**
     * Session constructor.
     *
     * @param SessionManager $manager
     */
    public function __construct(SessionManager $sessionManager, CacheItemPoolInterface $sessionCachePool)
    {
        $this->sessionManager = $sessionManager;

        $this->sessionCachePool = $sessionCachePool;

        $this->config = $this->sessionManager->getConfig();

        $this->http = $this->sessionManager->getHttp();

        $this->getSessionId();
    }

    /**
     * Session destructor.
     */
    public function __destruct()
    {
        $this->saveData();
    }

    /**
     * Gets the session id.
     *
     * @return string|null
     */
    public function getSessionId()
    {
        $config = $this->config;
        $http = $this->http;

        // TODO : Add extra security check for session/cookie tampering.

        if (strlen($http->getCookie($config->get('session_name'))) == $config->get('session_id_length')) {
            $this->setSessionId($http->getCookie($config->get('session_name')));

            return $this->sessionId;
        } else {
            if ($config->get('session_id_type') == Config::TYPE_STR) {
                $this->setSessionId(Random::randStr($config->get('session_id_length')));
            } elseif ($config->get('session_id_type') == Config::TYPE_NUMBER) {
                $this->setSessionId(Random::randNumStr($config->get('session_id_length')));
            }

            try {
                $http->setCookie(
                    $config->get('session_name'),
                    $this->sessionId,
                    $config->get('session_expire') + time()
                );
            } catch (\Exception $e) {
                $e->getMessage();
            }

            return $this->sessionId;
        }
    }

    /**
     * Sets the session id.
     *
     * @param $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        $this->readData();
    }

    /**
     * Gets the session element of data by name.
     *
     * @param string|null $name
     * @return array|mixed|null
     */
    public function get(string $name = null)
    {
        if ($name === null) {
            return $this->data;
        }

        $path = explode('.', $name);
        $value = $this->data;

        foreach ($path as $item){
            if($item == ''){
                break;
            }

            $value = isset($value[$item]) ? $value[$item] : null;
        }

        return $value;
    }

    /**
     * Sets the session element of data by name.
     *
     * @param string $name
     * @param $value
     * @return $this
     */
    public function set(string $name, $value)
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * Reads the data from the storage.
     */
    protected function readData()
    {
        $this->sessionCacheItem = $this->sessionCachePool->getItem(
            $this->config->get('session_storage_prefix')
            . $this->sessionId
        );

        if($this->sessionCacheItem->isHit()) {
            $this->data = unserialize($this->sessionCacheItem->get());
        }

        return true;
    }

    /**
     * Saves the data in the storage.
     *
     * @return bool
     */
    protected function saveData()
    {
        $data = serialize($this->data);

        $this->sessionCacheItem->set($data);

        $this->sessionCacheItem->expiresAfter($this->config->get('session_expire'));

        return $this->sessionCachePool->save($this->sessionCacheItem);
    }
}