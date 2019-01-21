<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.1
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.0.0
 */

namespace AscmvcTest;

use Ascmvc\Mvc\Atlas;
use \Atlas\Orm\Atlas as AtlasORM;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AtlasTest extends TestCase
{
    public function testGetAtlasInstanceWillReturnFalseOnEmptyParams()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $baseConfig['atlas']['ORM']['aem1'] = [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'user'     => 'USERNAME',
            'password' => 'PASSWORD',
            'dbname'   => 'DATABASE',
        ];

        if (isset($baseConfig['atlas'])) {
            foreach ($baseConfig['atlas'] as $connType => $connections) {
                foreach ($connections as $connName => $params) {
                    $params = [];
                    $dbManager = Atlas::getInstance($connType, $connName, $params);
                }
            }
        }

        $this->assertFalse($dbManager);
    }

    public function testGetAtlasInstanceWillReturnFalseOnUnknownConnectionType()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $baseConfig['atlas']['DBAL']['aem1'] = [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'user'     => 'USERNAME',
            'password' => 'PASSWORD',
            'dbname'   => 'DATABASE',
        ];

        if (isset($baseConfig['atlas'])) {
            foreach ($baseConfig['atlas'] as $connType => $connections) {
                foreach ($connections as $connName => $params) {
                    $dbManager = Atlas::getInstance($connType, $connName, $params);
                }
            }
        }

        $this->assertFalse($dbManager);
    }

    public function testGetAtlasORMInstance()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $baseConfig['atlas']['ORM']['aem1'] = [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'user'     => 'USERNAME',
            'password' => 'PASSWORD',
            'dbname'   => 'DATABASE',
        ];

        if (isset($baseConfig['atlas'])) {
            foreach ($baseConfig['atlas'] as $connType => $connections) {
                foreach ($connections as $connName => $params) {
                    $dbManager = Atlas::getInstance($connType, $connName, $params);
                }
            }
        }

        $this->assertInstanceOf(AtlasOrm::class, $dbManager);
    }
}
