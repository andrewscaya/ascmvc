<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.3.0
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.1.0
 */

namespace AscmvcTest\Session;

use Ascmvc\Session\Config;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ConfigTest extends TestCase
{
    public function testGetElementFromConfigurationData()
    {
        $config = new Config();

        $this->assertSame('PHPSESSION', $config->get('session_name'));
    }

    public function testGetElementFromConfigurationDataWithEmptyStringParameter()
    {
        $config = new Config();

        $this->assertSame(
            [
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
            ],
            $config->get('')
        );
    }

    public function testGetElementFromConfigurationDataWithMergedArray()
    {
        $config = new Config(['session_name' => 'NEWPHPSESSION']);

        $this->assertSame('NEWPHPSESSION', $config->get('session_name'));
    }

    public function testGetElementFromConfigurationDataWillReturnNullWhenElementNotFound()
    {
        $config = new Config();

        $this->assertNull($config->get('wrong_key'));
    }

    public function testGetElementFromConfigurationDataWhenElementIsSet()
    {
        $config = new Config();

        $config->set('newelement', 'mydata');

        $this->assertSame('mydata', $config->get('newelement'));
    }
}
