<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    5.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.1.0
 *
 * @see       https://github.com/itxiao6/session for the canonical source repository
 * @copyright Copyright (c) 2018  戒尺 包描述
 * @license   https://opensource.org/licenses/MIT
 */

namespace Ascmvc\Session;

/**
 * Class Swoole
 *
 * @package Ascmvc\Session
 */
class Swoole
{
    // @codeCoverageIgnoreStart
    /**
     * Contains a swoole_http_request instance.
     * @var \swoole_http_request
     */
    protected $request = null;

    /**
     * Contains a swoole_http_response instance.
     * @var \swoole_http_response
     */
    protected $response = null;

    /**
     * Swoole constructor.
     *
     * @param \swoole_http_request $request
     * @param \swoole_http_response $response
     */
    public function __construct(\swoole_http_request $request, \swoole_http_response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Sets the \swoole_http_request object.
     *
     * @param \swoole_http_request|null $request
     *
     * @return $this|\swoole_http_request
     */
    public function setRequest(\swoole_http_request $request = null)
    {
        if ($request != null) {
            $this->request = $request;
            return $this;
        }
        return $this->request;
    }

    /**
     * Sets the \swoole_http_response object.
     *
     * @param \swoole_http_response|null $response
     *
     * @return $this|\swoole_http_response
     */
    public function setResponse(\swoole_http_response $response = null)
    {
        if ($response != null) {
            $this->response = $response;
            return $this;
        }
        return $this->response;
    }

    /**
     * Gets the cookie from the Swoole Request object.
     *
     * @param string $name
     *
     * @return null
     */
    public function getCookie($name = '')
    {
        $path = explode('.', $name);

        $value = $this->request->cookie;

        foreach ($path as $item) {
            if ($item == '') {
                break;
            }

            $value = isset($value[$item]) ? $value[$item] : null;
        }

        return $value;
    }

    /**
     * Sets the cookie in the Swoole Response object.
     *
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     *
     * @return mixed
     */
    public function setCookie(string $name, string $value = "", int $expire = 0, string $path = "", string $domain = "", bool $secure = false, bool $httponly = false)
    {
        return $this->response->cookie(...func_get_args());
    }

    /**
     * Returns the equivalent of the $_SERVER array from the Swoole environment.
     *
     * @return mixed
     */
    public function getServerGlobalEnv()
    {
        return array_merge($this->request->server, $this->request->header);
    }
    // @codeCoverageIgnoreEnd
}
