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
 * Class Random
 *
 * @package Ascmvc\Session
 */
class Random
{
    /**
     * Generates a random string of the specified length.
     *
     * @param $length
     * @return bool|string
     */
    public static function randStr($length)
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }

    /**
     * Generates a random string of numerical values of the specified length.
     *
     * @param $length
     *
     * @return string
     */
    public static function randNumStr($length)
    {
        $password = '';

        for ($i = 0; strlen($password) < $length; $i++) {
            $password .= (string) random_int(0, 9);
        }

        return $password;
    }
}
