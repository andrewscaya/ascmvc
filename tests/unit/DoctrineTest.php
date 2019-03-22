<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.2
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.0.0
 */

namespace AscmvcTest;

use Ascmvc\Mvc\Doctrine;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DoctrineTest extends TestCase
{
    public function testGetDoctrineInstanceWillReturnFalseOnEmptyParams()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $baseConfig['doctrine']['DBAL']['dm1'] = [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'user'     => 'USERNAME',
            'password' => 'PASSWORD',
            'dbname'   => 'DATABASE',
        ];

        if (isset($baseConfig['doctrine'])) {
            foreach ($baseConfig['doctrine'] as $connType => $connections) {
                foreach ($connections as $connName => $params) {
                    $params = [];
                    $dbManager = Doctrine::getInstance($connType, $connName, $params);
                }
            }
        }

        $this->assertFalse($dbManager);
    }

    public function testGetDoctrineInstanceWillReturnFalseOnUnknownConnectionType()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $baseConfig['doctrine']['WRONG']['dm1'] = [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'user'     => 'USERNAME',
            'password' => 'PASSWORD',
            'dbname'   => 'DATABASE',
        ];

        if (isset($baseConfig['doctrine'])) {
            foreach ($baseConfig['doctrine'] as $connType => $connections) {
                foreach ($connections as $connName => $params) {
                    $dbManager = Doctrine::getInstance($connType, $connName, $params);
                }
            }
        }

        $this->assertFalse($dbManager);
    }

    public function testGetDoctrineDBALnstance()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $baseConfig['doctrine']['DBAL']['dm1'] = [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'user'     => 'USERNAME',
            'password' => 'PASSWORD',
            'dbname'   => 'DATABASE',
        ];

        if (isset($baseConfig['doctrine'])) {
            foreach ($baseConfig['doctrine'] as $connType => $connections) {
                foreach ($connections as $connName => $params) {
                    $dbManager = Doctrine::getInstance($connType, $connName, $params);
                }
            }
        }

        $this->assertInstanceOf(Connection::class, $dbManager);
    }

    public function testGetDoctrineORMInstance()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $baseConfig['doctrine']['ORM']['em1'] = [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'user'     => 'USERNAME',
            'password' => 'PASSWORD',
            'dbname'   => 'DATABASE',
        ];

        if (isset($baseConfig['doctrine'])) {
            foreach ($baseConfig['doctrine'] as $connType => $connections) {
                foreach ($connections as $connName => $params) {
                    $dbManager = Doctrine::getInstance($connType, $connName, $params);
                }
            }
        }

        $this->assertInstanceOf(EntityManager::class, $dbManager);
    }
}
