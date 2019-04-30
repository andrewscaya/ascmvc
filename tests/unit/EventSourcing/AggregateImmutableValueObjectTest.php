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
 * @since      3.0.0
 */

namespace AscmvcTest;

use Ascmvc\EventSourcing\AggregateImmutableValueObject;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AggregateImmutableValueObjectTest extends TestCase
{
    public function testCreateAggregateImmutableValueObjectInstance()
    {
        $aggregateValueObject = new AggregateImmutableValueObject(['testkey' => 'testvalue']);

        $serializedString = 'a:1:{s:7:"testkey";s:9:"testvalue";}';

        $this->assertInstanceOf(AggregateImmutableValueObject::class, $aggregateValueObject);

        $this->assertSame(
            $serializedString,
            $aggregateValueObject->serialize()
        );

        $this->assertSame(
            $serializedString,
            $aggregateValueObject->__toString()
        );

        $this->assertInstanceOf(
            AggregateImmutableValueObject::class,
            $aggregateValueObject->unserialize($serializedString)
        );

        $this->assertSame(
            ['testkey' => 'testvalue'],
            $aggregateValueObject->unserialize($serializedString)->getProperties()
        );

        $this->assertSame(
            ['testkey' => 'testvalue'],
            $aggregateValueObject->hydrateToArray()
        );

        $this->assertFalse($aggregateValueObject->unserialize('O:8:"stdClass":0:{}'));

        $aggregateValueObject = new AggregateImmutableValueObject();

        $this->assertInstanceOf(AggregateImmutableValueObject::class, $aggregateValueObject);

        $this->assertTrue(is_array($aggregateValueObject->getProperties()));
    }
}
