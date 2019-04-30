<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.1.0
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.1.0
 */

namespace AscmvcTest\Session;

use Ascmvc\Session\Cache\DoctrineCacheItemPool;
use Ascmvc\Session\Config;
use Ascmvc\Session\Http;
use Ascmvc\Session\Session;
use Ascmvc\Session\SessionManager;
use Ascmvc\Session\Swoole;
use Doctrine\Common\Cache\FilesystemCache;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SessionTest extends TestCase
{
    public function testSessionConstructor()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn('a:1:{s:8:"testdata";s:6:"mydata";}');
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Firefox Test Browser';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $this->assertInstanceOf(Session::class, $sessionManager->getSession());
    }

    public function testSessionConstructorWithNoDataInCache()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturnFalse();
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Firefox Test Browser';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $this->assertInstanceOf(Session::class, $sessionManager->getSession());
    }

    public function testSessionConstructorWithAlnumSessionIdByDefault()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn('a:1:{s:8:"testdata";s:6:"mydata";}');
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Firefox Test Browser';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 3,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $session = $sessionManager->getSession();

        $this->assertInstanceOf(Session::class, $session);

        $sessionReflection = new \ReflectionClass($session);

        $propertySessionId = $sessionReflection->getProperty('sessionId');

        $propertySessionId->setAccessible(true);

        $sessionId = $propertySessionId->getValue($session);

        $this->assertTrue(ctype_alnum($sessionId));
    }

    public function testSessionConstructorWithNumericSessionId()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn('a:1:{s:8:"testdata";s:6:"mydata";}');
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Firefox Test Browser';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 2,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $session = $sessionManager->getSession();

        $this->assertInstanceOf(Session::class, $session);

        $sessionReflection = new \ReflectionClass($session);

        $propertySessionId = $sessionReflection->getProperty('sessionId');

        $propertySessionId->setAccessible(true);

        $sessionId = $propertySessionId->getValue($session);

        $this->assertTrue(ctype_digit($sessionId));
    }

    public function testSessionConstructorWithValidCookie()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $time = time();

        $cacheString =
            'a:5:{s:9:"initiated";b:1;s:7:"created";i:' . $time . ';'
            . 's:15:"http_user_agent";s:64:"d33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1";'
            . 's:13:"last_activity";i:' . $time . ';s:10:"middleware";'
            . 'a:2:{s:7:"session";s:40:"Application\Middleware\SessionMiddleware";s:7:"example";i:3;}}';

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn($cacheString);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0';

        $_COOKIE['PHPSESSION'] = '680588c982e49a434282e369ad4371f8';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $session = $sessionManager->getSession();

        $this->assertInstanceOf(Session::class, $session);

        $sessionReflection = new \ReflectionClass($session);

        $propertySessionId = $sessionReflection->getProperty('sessionId');

        $propertySessionId->setAccessible(true);

        $sessionId = $propertySessionId->getValue($session);

        $this->assertSame('680588c982e49a434282e369ad4371f8', $sessionId);

        $this->assertEquals(3, $session->get('middleware')['example']);

        $lastActivity = $session->get('last_activity');

        $this->assertSame(
            [
                'initiated' => true,
                'created' => $time,
                'http_user_agent' => 'd33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1',
                'last_activity' => $lastActivity,
                'middleware' => [
                    'session' => 'Application\Middleware\SessionMiddleware',
                    'example' => 3
                ],
            ],
            $session->get()
        );
    }

    public function testSessionConstructorWithExpiredSessionCreatedBeyondAllowedLimit()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $time1 = time() - (60 * 30) - 1;

        $time2 = time() - 60;

        $cacheString =
            'a:5:{s:9:"initiated";b:1;s:7:"created";i:' . $time1 . ';'
            . 's:15:"http_user_agent";s:64:"d33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1";'
            . 's:13:"last_activity";i:' . $time2 . ';s:10:"middleware";'
            . 'a:2:{s:7:"session";s:40:"Application\Middleware\SessionMiddleware";s:7:"example";i:3;}}';

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn($cacheString);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();
        $filesystemCacheMock
            ->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0';

        $_COOKIE['PHPSESSION'] = '680588c982e49a434282e369ad4371f8';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $session = $sessionManager->getSession();

        $this->assertInstanceOf(Session::class, $session);

        $sessionReflection = new \ReflectionClass($session);

        $propertySessionId = $sessionReflection->getProperty('sessionId');

        $propertySessionId->setAccessible(true);

        $sessionId = $propertySessionId->getValue($session);

        $this->assertNotSame('680588c982e49a434282e369ad4371f8', $sessionId);

        $this->assertEquals(3, $session->get('middleware')['example']);

        $this->assertSame(
            'd33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1',
            $session->get('http_user_agent')
        );
    }

    public function testSessionConstructorWithExpiredSessionLastActivityBeyondAllowedLimit()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $time1 = time() - (60 * 20);

        $time2 = time() - (60 * 30) - 1;

        $cacheString =
            'a:5:{s:9:"initiated";b:1;s:7:"created";i:' . $time1 . ';'
            . 's:15:"http_user_agent";s:64:"d33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1";'
            . 's:13:"last_activity";i:' . $time2 . ';s:10:"middleware";'
            . 'a:2:{s:7:"session";s:40:"Application\Middleware\SessionMiddleware";s:7:"example";i:3;}}';

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn($cacheString);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();
        $filesystemCacheMock
            ->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0';

        $_COOKIE['PHPSESSION'] = '680588c982e49a434282e369ad4371f8';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $session = $sessionManager->getSession();

        $this->assertInstanceOf(Session::class, $session);

        $sessionReflection = new \ReflectionClass($session);

        $propertySessionId = $sessionReflection->getProperty('sessionId');

        $propertySessionId->setAccessible(true);

        $sessionId = $propertySessionId->getValue($session);

        $this->assertNotSame('680588c982e49a434282e369ad4371f8', $sessionId);

        $this->assertEquals(3, $session->get('middleware')['example']);

        $this->assertSame(
            'd33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1',
            $session->get('http_user_agent')
        );
    }

    public function testSessionConstructorWithInvalidSessionWithoutInitiatedIndex()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $time1 = time() - (60 * 20);

        $time2 = time() - 60;

        $cacheString =
            'a:4:{s:7:"created";i:' . $time1 . ';'
            . 's:15:"http_user_agent";s:64:"d33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1";'
            . 's:13:"last_activity";i:' . $time2 . ';s:10:"middleware";'
            . 'a:2:{s:7:"session";s:40:"Application\Middleware\SessionMiddleware";s:7:"example";i:3;}}';

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn($cacheString);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();
        $filesystemCacheMock
            ->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0';

        $_COOKIE['PHPSESSION'] = '680588c982e49a434282e369ad4371f8';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $session = $sessionManager->getSession();

        $this->assertInstanceOf(Session::class, $session);

        $sessionReflection = new \ReflectionClass($session);

        $propertySessionId = $sessionReflection->getProperty('sessionId');

        $propertySessionId->setAccessible(true);

        $sessionId = $propertySessionId->getValue($session);

        $this->assertNotSame('680588c982e49a434282e369ad4371f8', $sessionId);

        $this->assertEquals(3, $session->get('middleware')['example']);

        $this->assertSame(
            'd33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1',
            $session->get('http_user_agent')
        );
    }

    public function testSessionConstructorWithInvalidSessionWithoutCreatedIndex()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $time1 = time() - (60 * 20);

        $time2 = time() - 60;

        $cacheString =
            'a:4:{s:9:"initiated";b:1;'
            . 's:15:"http_user_agent";s:64:"d33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1";'
            . 's:13:"last_activity";i:' . $time2 . ';s:10:"middleware";'
            . 'a:2:{s:7:"session";s:40:"Application\Middleware\SessionMiddleware";s:7:"example";i:3;}}';

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn($cacheString);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();
        $filesystemCacheMock
            ->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0';

        $_COOKIE['PHPSESSION'] = '680588c982e49a434282e369ad4371f8';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $session = $sessionManager->getSession();

        $this->assertInstanceOf(Session::class, $session);

        $sessionReflection = new \ReflectionClass($session);

        $propertySessionId = $sessionReflection->getProperty('sessionId');

        $propertySessionId->setAccessible(true);

        $sessionId = $propertySessionId->getValue($session);

        $this->assertNotSame('680588c982e49a434282e369ad4371f8', $sessionId);

        $this->assertEquals(3, $session->get('middleware')['example']);

        $this->assertSame(
            'd33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1',
            $session->get('http_user_agent')
        );
    }

    public function testSessionConstructorWithInvalidSessionWithoutLastActivityIndex()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $time1 = time() - (60 * 20);

        $time2 = time() - 60;

        $cacheString =
            'a:4:{s:9:"initiated";b:1;s:7:"created";i:' . $time1 . ';'
            . 's:15:"http_user_agent";s:64:"d33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1";'
            . 's:10:"middleware";'
            . 'a:2:{s:7:"session";s:40:"Application\Middleware\SessionMiddleware";s:7:"example";i:3;}}';

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn($cacheString);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();
        $filesystemCacheMock
            ->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0';

        $_COOKIE['PHPSESSION'] = '680588c982e49a434282e369ad4371f8';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $session = $sessionManager->getSession();

        $this->assertInstanceOf(Session::class, $session);

        $sessionReflection = new \ReflectionClass($session);

        $propertySessionId = $sessionReflection->getProperty('sessionId');

        $propertySessionId->setAccessible(true);

        $sessionId = $propertySessionId->getValue($session);

        $this->assertNotSame('680588c982e49a434282e369ad4371f8', $sessionId);

        $this->assertEquals(3, $session->get('middleware')['example']);

        $this->assertSame(
            'd33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1',
            $session->get('http_user_agent')
        );
    }

    public function testSessionConstructorWithInvalidSessionWithoutUserAgentIndex()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $time1 = time() - (60 * 20);

        $time2 = time() - 60;

        $cacheString =
            'a:4:{s:9:"initiated";b:1;s:7:"created";i:' . $time1 . ';'
            . 's:13:"last_activity";i:' . $time2 . ';s:10:"middleware";'
            . 'a:2:{s:7:"session";s:40:"Application\Middleware\SessionMiddleware";s:7:"example";i:3;}}';

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn($cacheString);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();
        $filesystemCacheMock
            ->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0';

        $_COOKIE['PHPSESSION'] = '680588c982e49a434282e369ad4371f8';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $session = $sessionManager->getSession();

        $this->assertInstanceOf(Session::class, $session);

        $sessionReflection = new \ReflectionClass($session);

        $propertySessionId = $sessionReflection->getProperty('sessionId');

        $propertySessionId->setAccessible(true);

        $sessionId = $propertySessionId->getValue($session);

        $this->assertNotSame('680588c982e49a434282e369ad4371f8', $sessionId);

        $this->assertEquals(3, $session->get('middleware')['example']);

        $this->assertSame(
            'd33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1',
            $session->get('http_user_agent')
        );
    }

    public function testSessionConstructorWithInvalidSessionWithoutValidUserAgentIndex()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $time1 = time() - (60 * 20);

        $time2 = time() - 60;

        $cacheString =
            'a:5:{s:9:"initiated";b:1;s:7:"created";i:' . $time1 . ';'
            . 's:15:"http_user_agent";s:64:"d33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adaa1";'
            . 's:13:"last_activity";i:' . $time2 . ';s:10:"middleware";'
            . 'a:2:{s:7:"session";s:40:"Application\Middleware\SessionMiddleware";s:7:"example";i:3;}}';

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn($cacheString);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();
        $filesystemCacheMock
            ->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0';

        $_COOKIE['PHPSESSION'] = '680588c982e49a434282e369ad4371f8';

        $config = new Config(
            [
                'enabled' => true,
                'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
                'doctrine_cache_driver' => FilesystemCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
                //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
                'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
                'doctrine_cache_server_params' => [
                    'host' => '127.0.0.1',
                    'port' => 6379, // redis
                    //'port' => 11211 // memcached/memcache
                ],
                'session_name' => 'PHPSESSION',
                'session_path' => '/',
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $sessionManager->start();

        $session = $sessionManager->getSession();

        $this->assertInstanceOf(Session::class, $session);

        $sessionReflection = new \ReflectionClass($session);

        $propertySessionId = $sessionReflection->getProperty('sessionId');

        $propertySessionId->setAccessible(true);

        $sessionId = $propertySessionId->getValue($session);

        $this->assertNotSame('680588c982e49a434282e369ad4371f8', $sessionId);

        $this->assertEquals(3, $session->get('middleware')['example']);

        $this->assertSame(
            'd33bdc0532cfe79bd15ad8554774fe2a77af2977642d261e2ec2462f191adae1',
            $session->get('http_user_agent')
        );
    }
}
