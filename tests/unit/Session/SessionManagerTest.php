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
class SessionManagerTest extends TestCase
{
    public function testGetSessionManagerConstructorWithEmptyParameters()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $sessionManager1 = SessionManager::getSessionManager(null, null, null);

        $this->assertInstanceOf(SessionManager::class, $sessionManager1);

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
            $sessionManager1->getConfig()->get()
        );

        $sessionManager2 = SessionManager::getSessionManager(null, null, null);

        $this->assertTrue($sessionManager1 === $sessionManager2);
    }

    public function testGetSessionManagerConstructorWithEmptyParametersWithReset()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $sessionManager1 = SessionManager::getSessionManager(null, null, null);

        $this->assertInstanceOf(SessionManager::class, $sessionManager1);

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
            $sessionManager1->getConfig()->get()
        );

        $sessionManager2 = SessionManager::getSessionManager(null, null, null, true);

        $this->assertInstanceOf(SessionManager::class, $sessionManager2);

        $this->assertFalse($sessionManager1 === $sessionManager2);
    }

    public function testGetSessionManagerConstructorWithSpecifiedConfigurationAndSetNewConfiguration()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
                'session_domain' => 'localdomain.local',
                'session_secure' => false,
                'session_httponly' => false,
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_token_regeneration' => 60 * 30, // 30 minutes
                'session_expire' => 60 * 60, // 60 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $this->assertInstanceOf(SessionManager::class, $sessionManager);

        $this->assertSame(
            array_merge([
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
            ], [
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
                'session_domain' => 'localdomain.local',
                'session_secure' => false,
                'session_httponly' => false,
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_token_regeneration' => 60 * 30, // 30 minutes
                'session_expire' => 60 * 60, // 60 minutes
            ]),
            $sessionManager->getConfig()->get()
        );

        $config2 = new Config(
            [
                'enabled' => false,
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
                'session_domain' => 'localdomain.local',
                'session_secure' => false,
                'session_httponly' => false,
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_token_regeneration' => 60 * 30, // 30 minutes
                'session_expire' => 60 * 60, // 60 minutes
            ]
        );

        $sessionManager->setConfig($config2);

        $this->assertSame(
            array_merge([
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
            ], [
                'enabled' => false,
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
                'session_domain' => 'localdomain.local',
                'session_secure' => false,
                'session_httponly' => false,
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_token_regeneration' => 60 * 30, // 30 minutes
                'session_expire' => 60 * 60, // 60 minutes
            ]),
            $sessionManager->getConfig()->get()
        );
    }

    public function testGetSessionManagerConstructorWithEmptyParametersWillNotStartSessionAndWillNotUseHttpObjectByDefault()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $sessionManager = SessionManager::getSessionManager(null, null, null, true);

        $this->assertInstanceOf(SessionManager::class, $sessionManager);

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
            $sessionManager->getConfig()->get()
        );

        $this->assertFalse($sessionManager->start());

        $this->assertNull($sessionManager->getHttp());
    }

    public function testGetSessionManagerConstructorWithSpecifiedConfigurationWillStartSessionWithHttpObjectByDefault()
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
                'session_domain' => 'localdomain.local',
                'session_secure' => false,
                'session_httponly' => false,
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_token_regeneration' => 60 * 30, // 30 minutes
                'session_expire' => 60 * 60, // 60 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $this->assertInstanceOf(SessionManager::class, $sessionManager->start());

        $this->assertInstanceOf(Http::class, $sessionManager->getHttp());

        $this->assertInstanceOf(Session::class, $sessionManager->getSession());
    }

    public function testGetSessionManagerConstructorWithSpecifiedConfigurationWillStartSessionWithSwooleObject()
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

        $swooleRequestMock = \Mockery::mock('overload:' . \swoole_http_request::class);
        $swooleRequestMock->cookie = [];
        $swooleRequestMock->server = [];
        $swooleRequestMock->header = [];

        $swooleResponseMock = \Mockery::mock('overload:' . \swoole_http_response::class);

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
                'session_domain' => 'localdomain.local',
                'session_secure' => false,
                'session_httponly' => false,
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_token_regeneration' => 60 * 30, // 30 minutes
                'session_expire' => 60 * 60, // 60 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager($swooleRequestMock, $swooleResponseMock, $config, true);

        $this->assertInstanceOf(SessionManager::class, $sessionManager->start());

        $this->assertInstanceOf(Swoole::class, $sessionManager->getHttp());

        $this->assertInstanceOf(Session::class, $sessionManager->getSession());
    }

    public function testGetSessionManagerConstructorWithSpecifiedConfigurationWillStartSessionWithHttpObjectByDefaultAndSetSessionToNullWhenPersistingSession()
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
                'session_domain' => 'localdomain.local',
                'session_secure' => false,
                'session_httponly' => false,
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_token_regeneration' => 60 * 30, // 30 minutes
                'session_expire' => 60 * 60, // 60 minutes
            ]
        );

        $sessionManager = SessionManager::getSessionManager(null, null, $config, true);

        $this->assertInstanceOf(SessionManager::class, $sessionManager->start());

        $this->assertInstanceOf(Http::class, $sessionManager->getHttp());

        $this->assertInstanceOf(Session::class, $sessionManager->getSession());

        $sessionManager->persist();

        $this->assertNull($sessionManager->getSession());
    }
}
