<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.1.0
 *
 * @see       https://github.com/itxiao6/session for the canonical source repository
 * @copyright Copyright (c) 2018  戒尺 包描述
 * @license   https://opensource.org/licenses/MIT
 */

namespace Ascmvc\Session;

use Psr\Cache\CacheItemInterface;
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
     * @param SessionManager $sessionManager
     * @param CacheItemPoolInterface $sessionCachePool
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
    protected function getSessionId()
    {
        $config = $this->config;
        $http = $this->http;
        $cookie = $http->getCookie($config->get('session_name'));
        $useragent = $http->getServerGlobalEnv()['user-agent'] ?? $_SERVER['HTTP_USER_AGENT'];
        $useragent .= $config->get('session_name');

        if (strlen($cookie) == $config->get('session_id_length')) {
            $this->setSessionId($cookie);

            $this->readData();

            if (!is_null($this->get('initiated'))
                && !is_null($this->get('http_user_agent'))
                && $this->get('http_user_agent') == hash('sha256', $useragent)
                && !is_null($this->get('last_activity'))
                && (time() - $this->get('last_activity') <= $config->get('session_expire')
                    || $config->get('session_expire') == 0)
                && !is_null($this->get('created'))
                && (time() - $this->get('created') <= $config->get('session_token_regeneration')
                    || $config->get('session_token_regeneration') == 0)
            ) {
                // Update last activity timestamp.
                $this->set('last_activity', time());

                return $this->sessionId;
            } else {
                if (!is_null($this->get('created'))
                    && $config->get('session_token_regeneration') != 0
                    && (time() - $this->get('created') > $config->get('session_token_regeneration'))
                ) {
                    $this->regenerateSession();
                } elseif (!is_null($this->get('last_activity'))
                    && $config->get('session_expire') != 0
                    && (time() - $this->get('last_activity') > $config->get('session_expire'))
                ) {
                    $this->sessionCacheItem = $this->sessionCachePool->deleteItem(
                        $this->config->get('session_storage_prefix')
                        . $this->sessionId
                    );

                    $this->createNewSession();
                } else {
                    $this->createNewSession();
                }

                return $this->sessionId;
            }
        } else {
            $this->createNewSession();

            return $this->sessionId;
        }
    }

    /**
     * Sets the session id.
     *
     * @param $sessionId
     * @return null|string
     */
    protected function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this->sessionId;
    }

    /**
     * Generates a new session ID.
     *
     * @return bool|string
     */
    protected function generateSessionId()
    {
        $config = $this->config;

        // Create session ID.
        if ($config->get('session_id_type') == Config::TYPE_NUMBER) {
            return Random::randNumStr($config->get('session_id_length'));
        } else {
            return Random::randStr($config->get('session_id_length'));
        }
    }

    /**
     * Creates a new session.
     */
    protected function createNewSession()
    {
        $config = $this->config;
        $http = $this->http;

        try {
            if (isset($this->sessionId)) {
                $http->setCookie(
                    $config->get('session_name'),
                    $this->sessionId,
                    time() - 86400,
                    $config->get('session_path'),
                    $config->get('session_domain'),
                    $config->get('session_secure'),
                    $config->get('session_httponly')
                );
            }

            $this->setSessionId($this->generateSessionId());

            $this->data = [];

            $this->readData();

            // Avoid session hijacking.
            $useragent = $http->getServerGlobalEnv()['user-agent'] ?? $_SERVER['HTTP_USER_AGENT'];
            $useragent .= $config->get('session_name');
            $this->set('http_user_agent', hash('sha256', $useragent));

            // Avoid session fixation.
            $this->set('initiated', true);

            $this->set('last_activity', time());

            $this->set('created', time());

            $data = serialize($this->data);

            $this->sessionCacheItem->set($data);

            $this->sessionCachePool->save($this->sessionCacheItem);

            $http->setCookie(
                $config->get('session_name'),
                $this->sessionId,
                0,
                $config->get('session_path'),
                $config->get('session_domain'),
                $config->get('session_secure'),
                $config->get('session_httponly')
            );
        // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $e->getMessage();
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Regenerates an expired session.
     */
    protected function regenerateSession()
    {
        $config = $this->config;
        $http = $this->http;

        try {
            $http->setCookie(
                $config->get('session_name'),
                $this->sessionId,
                time() - 86400,
                $config->get('session_path'),
                $config->get('session_domain'),
                $config->get('session_secure'),
                $config->get('session_httponly')
            );

            $this->sessionCacheItem->expiresAfter(300);

            $this->sessionCachePool->save($this->sessionCacheItem);

            $this->setSessionId($this->generateSessionId());

            $this->sessionCacheItem = $this->sessionCachePool->getItem(
                $config->get('session_storage_prefix')
                . $this->sessionId
            );

            $this->set('last_activity', time());

            $this->set('created', time());

            $data = serialize($this->data);

            $this->sessionCacheItem->set($data);

            $this->sessionCachePool->save($this->sessionCacheItem);

            $http->setCookie(
                $config->get('session_name'),
                $this->sessionId,
                0,
                $config->get('session_path'),
                $config->get('session_domain'),
                $config->get('session_secure'),
                $config->get('session_httponly')
            );
        // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            $e->getMessage();
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Gets the session element of data by name.
     *
     * @param string|null $name
     * @return array|mixed|null
     */
    public function get(string $name = null)
    {
        $path = explode('.', $name);
        $value = $this->data;

        foreach ($path as $item) {
            if ($item == '') {
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
     *
     * @return bool
     */
    protected function readData()
    {
        $this->sessionCacheItem = $this->sessionCachePool->getItem(
            $this->config->get('session_storage_prefix')
            . $this->sessionId
        );

        if ($this->sessionCacheItem->isHit()) {
            $this->data = unserialize($this->sessionCacheItem->get());

            return true;
        }

        return false;
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

        return $this->sessionCachePool->save($this->sessionCacheItem);
    }
}
