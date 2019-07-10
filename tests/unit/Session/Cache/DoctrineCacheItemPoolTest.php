<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.0
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.1.0
 */

namespace AscmvcTest\Session\Cache;

use Ascmvc\Session\Cache\DoctrineCacheItem;
use Ascmvc\Session\Cache\DoctrineCacheItemPool;
use Ascmvc\Session\Cache\DoctrineInvalidArgumentException;
use Ascmvc\Session\Config;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\ClearableCache;
use Doctrine\Common\Cache\FileCache;
use Doctrine\Common\Cache\FilesystemCache;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DoctrineCacheItemPoolTest extends TestCase
{
    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndGetOneItem()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('fetch')
            ->once()
            ->andReturn(['testdata' => 'mydata']);

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $this->assertInstanceOf(DoctrineCacheItemPool::class, $doctrineCacheItemPool);

        $doctrineCacheItem = $doctrineCacheItemPool->getItem('1a1a1a');

        $this->assertInstanceOf(DoctrineCacheItem::class, $doctrineCacheItem);

        $this->assertSame(
            ['testdata' => 'mydata'],
            $doctrineCacheItem->get()
        );

        $this->expectException(DoctrineInvalidArgumentException::class);

        $doctrineCacheItem = $doctrineCacheItemPool->getItem('-');
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndGetCollectionItems()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock1 = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock1
            ->shouldReceive('fetch')
            ->withAnyArgs()
            ->times(3)
            ->andReturnValues(
                [
                    'testkey1' => ['testdata1' => 'mydata1'],
                    'testkey2' => ['testdata2' => 'mydata2'],
                    'testkey3' => ['testdata3' => 'mydata3'],
                ]
            );

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $doctrineCacheItems = $doctrineCacheItemPool->getItems(
            [
                '1a1a1a',
                '1b1b1b',
                '1c1c1c',
            ]
        );

        $this->assertInstanceOf(DoctrineCacheItem::class, $doctrineCacheItems[0]);
        $this->assertInstanceOf(DoctrineCacheItem::class, $doctrineCacheItems[1]);
        $this->assertInstanceOf(DoctrineCacheItem::class, $doctrineCacheItems[2]);

        $this->assertSame(
            ['testdata1' => 'mydata1'],
            $doctrineCacheItems[0]->get()
        );

        $this->assertSame(
            ['testdata2' => 'mydata2'],
            $doctrineCacheItems[1]->get()
        );

        $this->assertSame(
            ['testdata3' => 'mydata3'],
            $doctrineCacheItems[2]->get()
        );
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndHasOneItem()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('contains')
            ->once()
            ->andReturnTrue();

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $result = $doctrineCacheItemPool->hasItem('1a1a1a');

        $this->assertTrue($result);

        $this->expectException(DoctrineInvalidArgumentException::class);

        $doctrineCacheItem = $doctrineCacheItemPool->hasItem('-');
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndHasNotOneItem()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('contains')
            ->once()
            ->andReturnFalse();

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $result = $doctrineCacheItemPool->hasItem('1a1a1a');

        $this->assertFalse($result);
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndClearWithClearableInterface()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock(
            'overload:' . FilesystemCache::class,
            FileCache::class . ', '
            . CacheProvider::class . ', '
            . ClearableCache::class
        );
        $filesystemCacheMock
            ->shouldReceive('deleteAll')
            ->once()
            ->andReturnTrue();

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $result = $doctrineCacheItemPool->clear();

        $this->assertTrue($result);
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndClearWithoutClearableInterface()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $result = $doctrineCacheItemPool->clear();

        $this->assertFalse($result);
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndDeleteOneItem()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $result = $doctrineCacheItemPool->deleteItem('1a1a1a');

        $this->assertTrue($result);

        $this->expectException(DoctrineInvalidArgumentException::class);

        $doctrineCacheItem = $doctrineCacheItemPool->deleteItem('-');
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndCannotDeleteOneItem()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('delete')
            ->once()
            ->andReturnFalse();

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $result = $doctrineCacheItemPool->deleteItem('1a1a1a');

        $this->assertFalse($result);
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndDeleteCollectionItems()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock1 = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock1
            ->shouldReceive('delete')
            ->withAnyArgs()
            ->times(3)
            ->andReturnValues(
                [
                    true,
                    true,
                    true
                ]
            );

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $result = $doctrineCacheItemPool->deleteItems(
            [
                '1a1a1a',
                '1b1b1b',
                '1c1c1c',
            ]
        );

        $this->assertTrue($result);
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndCannotDeleteCollectionItems()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock1 = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock1
            ->shouldReceive('delete')
            ->withAnyArgs()
            ->times(3)
            ->andReturnValues(
                [
                    true,
                    false,
                    true
                ]
            );

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $result = $doctrineCacheItemPool->deleteItems(
            [
                '1a1a1a',
                '1b1b1b',
                '1c1c1c',
            ]
        );

        $this->assertFalse($result);
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndSaveOneItem()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $doctrineCacheItem = new DoctrineCacheItem('testkey', ['testdata' => 'mydata']);

        $result = $doctrineCacheItemPool->save($doctrineCacheItem);

        $this->assertTrue($result);
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndCannotSaveOneItem()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnFalse();

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $doctrineCacheItem = new DoctrineCacheItem('testkey', ['testdata' => 'mydata']);

        $result = $doctrineCacheItemPool->save($doctrineCacheItem);

        $this->assertFalse($result);
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndSaveOneItemDeferred()
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
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $doctrineCacheItem = new DoctrineCacheItem('testkey', ['testdata' => 'mydata']);

        $result = $doctrineCacheItemPool->saveDeferred($doctrineCacheItem);

        $this->assertTrue($result);

        $doctrineCacheItemPoolReflection = new \ReflectionClass($doctrineCacheItemPool);

        $propertyDeferred = $doctrineCacheItemPoolReflection->getProperty('deferred');

        $propertyDeferred->setAccessible(true);

        $this->assertTrue(key_exists('testkey', $propertyDeferred->getValue($doctrineCacheItemPool)));
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndSaveOneItemDeferredWhenAlreadyOneItemDeferredWithDifferentKeys()
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
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $doctrineCacheItem1 = new DoctrineCacheItem('testkey1', ['testdata1' => 'mydata1']);

        $result = $doctrineCacheItemPool->saveDeferred($doctrineCacheItem1);

        $this->assertTrue($result);

        $doctrineCacheItem2 = new DoctrineCacheItem('testkey2', ['testdata2' => 'mydata2']);

        $result = $doctrineCacheItemPool->saveDeferred($doctrineCacheItem2);

        $this->assertTrue($result);

        $doctrineCacheItemPoolReflection = new \ReflectionClass($doctrineCacheItemPool);

        $propertyDeferred = $doctrineCacheItemPoolReflection->getProperty('deferred');

        $propertyDeferred->setAccessible(true);

        $this->assertTrue(key_exists('testkey1', $propertyDeferred->getValue($doctrineCacheItemPool)));

        $this->assertTrue(key_exists('testkey2', $propertyDeferred->getValue($doctrineCacheItemPool)));
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndSaveOneItemDeferredWhenAlreadyOneItemDeferredWithSameKey()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnTrue();

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $doctrineCacheItem1 = new DoctrineCacheItem('testkey1', ['testdata1' => 'mydata1']);

        $result = $doctrineCacheItemPool->saveDeferred($doctrineCacheItem1);

        $this->assertTrue($result);

        $doctrineCacheItem2 = new DoctrineCacheItem('testkey1', ['testdata2' => 'mydata2']);

        $result = $doctrineCacheItemPool->saveDeferred($doctrineCacheItem2);

        $this->assertTrue($result);

        $doctrineCacheItemPoolReflection = new \ReflectionClass($doctrineCacheItemPool);

        $propertyDeferred = $doctrineCacheItemPoolReflection->getProperty('deferred');

        $propertyDeferred->setAccessible(true);

        $deferredArrayActual = $propertyDeferred->getValue($doctrineCacheItemPool);

        $this->assertTrue(key_exists('testkey1', $deferredArrayActual));

        $this->assertSame(
            ['testdata2' => 'mydata2'],
            $deferredArrayActual['testkey1']->get()
        );
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndSaveOneItemDeferredWhenAlreadyOneItemDeferredWithSameKeyFailed()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->once()
            ->andReturnFalse();

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $doctrineCacheItem1 = new DoctrineCacheItem('testkey1', ['testdata1' => 'mydata1']);

        $result = $doctrineCacheItemPool->saveDeferred($doctrineCacheItem1);

        $this->assertTrue($result);

        $doctrineCacheItem2 = new DoctrineCacheItem('testkey1', ['testdata2' => 'mydata2']);

        $result = $doctrineCacheItemPool->saveDeferred($doctrineCacheItem2);

        $this->assertFalse($result);

        $doctrineCacheItemPoolReflection = new \ReflectionClass($doctrineCacheItemPool);

        $propertyDeferred = $doctrineCacheItemPoolReflection->getProperty('deferred');

        $propertyDeferred->setAccessible(true);

        $deferredArrayActual = $propertyDeferred->getValue($doctrineCacheItemPool);

        $this->assertTrue(key_exists('testkey1', $deferredArrayActual));

        $this->assertSame(
            ['testdata1' => 'mydata1'],
            $deferredArrayActual['testkey1']->get()
        );
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndCommitEmptyDeferredItems()
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
                'session_id_length' => 32,
                'session_id_type' => 1,
                'session_storage_prefix' => 'ascmvc',
                'session_expire' => 60 * 30, // 30 minutes
            ]
        );

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $result = $doctrineCacheItemPool->commit();

        $this->assertTrue($result);
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndCommitDeferredItemsWithDifferentKeys()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->times(2)
            ->andReturnTrue();

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $doctrineCacheItem1 = new DoctrineCacheItem('testkey1', ['testdata1' => 'mydata1']);

        $result = $doctrineCacheItemPool->saveDeferred($doctrineCacheItem1);

        $this->assertTrue($result);

        $doctrineCacheItem2 = new DoctrineCacheItem('testkey2', ['testdata2' => 'mydata2']);

        $result = $doctrineCacheItemPool->saveDeferred($doctrineCacheItem2);

        $this->assertTrue($result);

        $result = $doctrineCacheItemPool->commit();

        $this->assertTrue($result);
    }

    public function testDoctrineCacheItemPoolWithMockedFilesystemCacheAndCommitDeferredItemsWithDifferentKeysAndOneFailure()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $filesystemCacheMock = \Mockery::mock('overload:' . FilesystemCache::class);
        $filesystemCacheMock
            ->shouldReceive('save')
            ->times(2)
            ->andReturnValues([true, false]);

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

        $doctrineCacheItemPool = new DoctrineCacheItemPool($config);

        $doctrineCacheItem1 = new DoctrineCacheItem('testkey1', ['testdata1' => 'mydata1']);

        $result = $doctrineCacheItemPool->saveDeferred($doctrineCacheItem1);

        $this->assertTrue($result);

        $doctrineCacheItem2 = new DoctrineCacheItem('testkey2', ['testdata2' => 'mydata2']);

        $result = $doctrineCacheItemPool->saveDeferred($doctrineCacheItem2);

        $this->assertTrue($result);

        $result = $doctrineCacheItemPool->commit();

        $this->assertFalse($result);

        $doctrineCacheItemPoolReflection = new \ReflectionClass($doctrineCacheItemPool);

        $propertyDeferred = $doctrineCacheItemPoolReflection->getProperty('deferred');

        $propertyDeferred->setAccessible(true);

        $deferredArrayActual = $propertyDeferred->getValue($doctrineCacheItemPool);

        $this->assertFalse(key_exists('testkey1', $deferredArrayActual));

        $this->assertTrue(key_exists('testkey2', $deferredArrayActual));

        $this->assertSame(
            ['testdata2' => 'mydata2'],
            $deferredArrayActual['testkey2']->get()
        );
    }
}
