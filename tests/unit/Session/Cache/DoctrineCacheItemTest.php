<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.1.0
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.1.0
 */

namespace AscmvcTest\Session\Cache;

use Ascmvc\Session\Cache\DoctrineCacheItem;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DoctrineCacheItemTest extends TestCase
{
    public function testDoctrineCacheItemWithHit()
    {
        $doctrineCacheItem = new DoctrineCacheItem('testkey', ['testdata' => 'mydata']);

        $this->assertInstanceOf(DoctrineCacheItem::class, $doctrineCacheItem);

        $this->assertTrue($doctrineCacheItem->isHit());

        $this->assertSame('testkey', $doctrineCacheItem->getKey());

        $this->assertSame(
            ['testdata' => 'mydata'],
            $doctrineCacheItem->get()
        );

        $doctrineCacheItem->set(['newtestdata' => 'mynewdata']);

        $this->assertSame(
            ['newtestdata' => 'mynewdata'],
            $doctrineCacheItem->get()
        );
    }

    public function testDoctrineCacheItemWithNoHit()
    {
        $doctrineCacheItem = new DoctrineCacheItem('testkey', false);

        $this->assertFalse($doctrineCacheItem->isHit());

        $this->assertNull($doctrineCacheItem->get());
    }

    public function testDoctrineCacheItemWithExpirationAt()
    {
        $expiration = new \DateTime('NOW');

        $expiration->modify('+1 hour');

        $time = $expiration->diff(new \DateTime('NOW'));

        $doctrineCacheItem = new DoctrineCacheItem('testkey', ['testdata' => 'mydata']);

        $doctrineCacheItem->expiresAt($expiration);

        $this->assertEquals($time->i * $time->s, $doctrineCacheItem->getExpiration());
    }

    public function testDoctrineCacheItemWithExpirationAtWithNegativeValue()
    {
        $expiration = new \DateTime('NOW');

        $expiration->modify('-1 hour');

        $time = $expiration->diff(new \DateTime('NOW'));

        $doctrineCacheItem = new DoctrineCacheItem('testkey', ['testdata' => 'mydata']);

        $doctrineCacheItem->expiresAt($expiration);

        $this->assertEquals(0, $doctrineCacheItem->getExpiration());
    }

    public function testDoctrineCacheItemWithExpirationAtWithIntegerValue()
    {
        $doctrineCacheItem = new DoctrineCacheItem('testkey', ['testdata' => 'mydata']);

        $doctrineCacheItem->expiresAt(10);

        $this->assertEquals(0, $doctrineCacheItem->getExpiration());
    }

    public function testDoctrineCacheItemWithExpirationAfter()
    {
        $expiration = new \DateTime('NOW');

        $expiration->modify('+1 hour');

        $time = $expiration->diff(new \DateTime('NOW'));

        $doctrineCacheItem = new DoctrineCacheItem('testkey', ['testdata' => 'mydata']);

        $doctrineCacheItem->expiresAfter($time);

        $this->assertEquals($time->i * $time->s, $doctrineCacheItem->getExpiration());
    }

    public function testDoctrineCacheItemWithExpirationAfterWithNegativeValue()
    {
        $expiration = new \DateTime('NOW');

        $expiration->modify('-1 hour');

        $time = $expiration->diff(new \DateTime('NOW'));

        $doctrineCacheItem = new DoctrineCacheItem('testkey', ['testdata' => 'mydata']);

        $doctrineCacheItem->expiresAfter($time);

        $this->assertEquals(0, $doctrineCacheItem->getExpiration());
    }

    public function testDoctrineCacheItemWithExpirationAfterWithIntegerValue()
    {
        $doctrineCacheItem = new DoctrineCacheItem('testkey', ['testdata' => 'mydata']);

        $doctrineCacheItem->expiresAfter(10);

        $this->assertEquals(0, $doctrineCacheItem->getExpiration());
    }
}