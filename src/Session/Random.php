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

class Random
{
    static function randStr($length)
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
        //return substr(str_shuffle("abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ23456789"), 0, $length);
    }

    static function randNumStr($length)
    {
        $chars = array(
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        );

        $password = '';

        while (strlen($password) <= $length) {
            $password .= (string) random_int(0, 9);
            //$password .= $chars[rand(0, 9)];
        }

        return $password;
    }
}