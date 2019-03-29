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
     * Contains the session id string.
     *
     * @var string|null
     */
    protected $sessionId = null;

    /**
     * Contains a Storage instance.
     *
     * @var Storage|null
     */
    protected $storage = null;

    /**
     * Contains the session's data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Session constructor.
     *
     * @param SessionManager $manager
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;

        $this->storage = new Storage($this->sessionManager->getDriver(), $this->sessionManager->getConfig());

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
        $config = $this->sessionManager->getConfig();
        $http = $this->sessionManager->getHttp();

        if (strlen($http->getCookie($config->get('session_name'))) == $config->get('session_id_length')) {
            $this->setSessionId($http->getCookie($config->get('session_name')));

            return $this->sessionId;
        }

        if ($config->get('session_id_type') == Config::TYPE_STR) {
            $this->setSessionId(Random::randStr($config->get('session_id_length')));
        } elseif ($config->get('session_id_type') == Config::TYPE_NUMBER) {
            $this->setSessionId(Random::randNumStr($config->get('session_id_length')));
        }

        $http->setCookie(
            $config->get('session_name'),
            $this->sessionId,
            $config->get('session_expire') + time()
        );

        return $this->sessionId;
    }

    /**
     * Sets the session id.
     *
     * @param $sessionId
     */
    public function setSessionId(string $sessionId)
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
            return $this -> data;
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
        $this->data = unserialize($this->storage->read($this->sessionId));
    }

    /**
     * Saves the data in the storage.
     *
     * @return bool
     */
    public function saveData()
    {
        return $this->storage->write($this->sessionId, $this->data);
    }
}