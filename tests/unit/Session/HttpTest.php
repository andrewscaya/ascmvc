<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.1
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.1.0
 */

namespace AscmvcTest\Session;

use Ascmvc\Session\Http;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class HttpTest extends TestCase
{
    public function testGetElementFromGlobalCookieArray()
    {
        $_COOKIE = [
            'session' => '1a1a1a'
        ];

        $http = new Http();

        $this->assertSame('1a1a1a', $http->getCookie('session'));
    }

    public function testGetElementFromGlobalCookieArrayWithEmptyStringParameter()
    {
        $_COOKIE = [
            'session' => '1a1a1a',
            'loggedin' => true,
        ];

        $http = new Http();

        $this->assertSame(
            [
                'session' => '1a1a1a',
                'loggedin' => true,
            ],
            $http->getCookie('')
        );
    }

    public function testGetElementFromGlobalCookieArrayWillReturnNullWhenElementNotFound()
    {
        $_COOKIE = [
            'session' => '1a1a1a',
            'loggedin' => true,
        ];

        $http = new Http();

        $this->assertNull($http->getCookie('wrong_key'));
    }

    public function testGetElementFromGlobalCookieArrayWhenElementIsSet()
    {
        $http = new Http();

        $this->assertTrue($http->setCookie('testcookie', '1a1a1a', 0));
    }

    public function testGetServerGlobalEnvWillReturnServerGlobalArray()
    {
        $_SERVER = [
            'testserver' => true,
        ];

        $http = new Http();

        $this->assertSame(
            [
                'testserver' => true,
            ],
            $http->getServerGlobalEnv()
        );
    }
}
