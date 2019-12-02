<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.3
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.1.0
 *
 * @see       https://github.com/itxiao6/session for the canonical source repository
 * @copyright Copyright (c) 2018  戒尺 包描述
 * @license   https://opensource.org/licenses/MIT
 */

namespace Ascmvc\Session;

/**
 * Class Http
 *
 * @package Ascmvc\Session
 */
class Http
{
    /**
     * Gets a cookie by name.
     *
     * @param string $name
     * @return null
     */
    public function getCookie($name = '')
    {
        $path = explode('.', $name);

        $value = $_COOKIE;

        foreach ($path as $item) {
            if ($item == '') {
                break;
            }

            $value = isset($value[$item]) ? $value[$item] : null;
        }

        return $value;
    }

    /**
     * Sets a new cookie.
     *
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return bool
     */
    public function setCookie(string $name, string $value = "", int $expire = 0, string $path = "", string $domain = "", bool $secure = false, bool $httponly = false)
    {
        return setcookie(...func_get_args());
    }

    /**
     * Returns the $_SERVER array.
     *
     * @return mixed
     */
    public function getServerGlobalEnv()
    {
        return $_SERVER;
    }
}
