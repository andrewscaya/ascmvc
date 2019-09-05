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
 * Class Config
 *
 * @package Ascmvc\Session
 */
class Config
{
    const TYPE_STR = 1;

    const TYPE_NUMBER = 2;

    /**
     * Contains the basic session data.
     *
     * @var array
     */
    protected $data = [
        'session_name' => 'PHPSESSION',
        'session_path' => '/',
        'session_domain' => 'localdomain.local',
        'session_secure' => false,
        'session_httponly' => false,
        'session_id_length' => 32,
        'session_id_type' => 1,
        'session_storage_prefix' => 'ascmvc',
        'session_token_regeneration' => 60 * 30, // 30 minutes
        'session_expire' => 60 * 60, // 60 minutes
    ];

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->data = array_merge($this->data, $config);
    }

    /**
     * Gets a session element of data.
     *
     * @param string $name
     * @return array|mixed|null
     */
    public function get($name = '')
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
     * Sets a session element of data.
     *
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }
}
