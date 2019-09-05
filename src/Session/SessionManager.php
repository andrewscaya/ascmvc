<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.1
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.1.0
 *
 * @see       https://github.com/itxiao6/session for the canonical source repository
 * @copyright Copyright (c) 2018  戒尺 包描述
 * @license   https://opensource.org/licenses/MIT
 */

namespace Ascmvc\Session;

/**
 * Class SessionManager
 *
 * @package Ascmvc\Session
 */
class SessionManager
{
    /**
     * Contains the SessionManager instance.
     *
     * @var SessionManager
     */
    protected static $sessionManager;

    /**
     * Contains the Swoole Request object.
     *
     * @var \swoole_http_request|null
     */
    protected $request;

    /**
     * Contains the Swoole Response object.
     *
     * @var \swoole_http_response|null
     */
    protected $response;

    /**
     * Contains the session Config object.
     *
     * @var Config|null
     */
    protected $config = null;

    /**
     * Contains the session flag.
     *
     * @var bool
     */
    protected $enabled = false;

    /**
     * Contains the session Http object.
     *
     * @var Http|null
     */
    protected $http = null;

    /**
     * Contains the Session object.
     *
     * @var Session|null
     */
    protected $session = null;

    /**
     * SessionManager constructor.
     *
     * @param \swoole_http_request|null $request
     * @param \swoole_http_response|null $response
     * @param Config|null $config
     */
    protected function __construct($request = null, $response = null, Config $config = null)
    {
        $this->request = $request;

        $this->response = $response;

        if (isset($config)) {
            $this->config = $config;

            $this->enabled = $this->config->get('enabled');
        } else {
            $this->config = new Config();
        }
    }

    /**
     * Gets the singleton SessionManager.
     *
     * @param \swoole_http_request|null $request
     * @param \swoole_http_response|null $response
     * @param Config|null $config
     * @param bool $reset
     * @return SessionManager|null|static
     */
    public static function getSessionManager(
        $request = null,
        $response = null,
        Config $config = null,
        bool $reset = false
    ) {
        if (!self::$sessionManager || $reset === true) {
            self::$sessionManager = new static($request, $response, $config);
        }

        return self::$sessionManager;
    }

    /**
     * Starts the session.
     *
     * @return $this
     * @throws \Exception
     */
    public function start()
    {
        if ($this->isEnabled()) {
            if ($this->request != null && $this->response != null) {
                if ($this->request instanceof \swoole_http_request
                    && $this->response instanceof \swoole_http_response
                ) {
                    $this->http = (new Swoole($this->request, $this->response));
                } else {
                    throw new \Exception('Request or Response invalid');
                }
            } else {
                $this->http = (new Http());
            }

            $sessionCachePoolName = $this->config->get('psr6_cache_pool');

            $sessionCachePool = new $sessionCachePoolName($this->config);

            $this->session = new Session($this, $sessionCachePool);

            return $this;
        } else {
            return false;
        }
    }

    /**
     * Persists the session data in the cache storage.
     */
    public function persist()
    {
        $this->session = null;
    }

    /**
     * Returns the Config instance.
     *
     * @return Config|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the Config instance.
     *
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Checks the session flag.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Gets the Http instance.
     *
     * @return Http|null
     */
    public function getHttp()
    {
        return $this->http;
    }

    /**
     * Gets the Session instance.
     *
     * @return Session|null
     */
    public function getSession()
    {
        return $this->session;
    }
}
