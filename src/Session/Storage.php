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

/**
 * Class Storage
 *
 * @package Ascmvc\Session
 */
class Storage
{
    /**
     * Contains an instance of the storage driver.
     *
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $driver;

    /**
     * Contains the session Config object.
     *
     * @var Config
     */
    protected $config;

    /**
     * Storage constructor.
     *
     * @param \Doctrine\Common\Cache\Cache $driver
     * @param Config $config
     */
    public function __construct(\Doctrine\Common\Cache\Cache $driver, Config $config)
    {
        $this->driver = $driver;
        $this->config = $config;
    }

    /**
     * Reads the data from storage.
     *
     * @param $sessionId
     * @return string
     */
    public function read($sessionId) : string
    {
        return $this->driver->fetch($this->config->get('session_storage_prefix') . $sessionId);
    }

    /**
     * Deletes the data from storage.
     *
     * @param $sessionId
     * @return bool
     */
    public function clear($sessionId) : bool
    {
        return $this->driver->save($this->config->get('session_storage_prefix') . $sessionId, null);
    }

    /**
     * Deletes the entire session from storage.
     *
     * @param $sessionId
     * @return bool
     */
    public function delete($sessionId) : bool
    {
        return $this->driver->delete($this->config->get('session_storage_prefix') . $sessionId);
    }

    /**
     * Saves the data to storage.
     *
     * @param $sessionId
     * @param $data
     * @return bool
     */
    public function write($sessionId, $data) : bool
    {
        return $this->driver->save(
            $this->config->get('session_storage_prefix') . $sessionId,
            serialize($data),
            $this->config->get('session_expire')
        );
    }
}