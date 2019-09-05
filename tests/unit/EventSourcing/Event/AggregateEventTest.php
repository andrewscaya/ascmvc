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
 * @since      3.0.0
 */

namespace AscmvcTest\EventSourcing\Event;

use Ascmvc\EventSourcing\AggregateImmutableValueObject;
use Ascmvc\EventSourcing\Event\AggregateEvent;
use Ascmvc\Mvc\App;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AggregateEventTest extends TestCase
{
    public function testCreateAggregateEventInstance()
    {
        $app = App::getInstance();

        $aggregateValueObject1 = new AggregateImmutableValueObject(['testkey1' => 'testvalue1']);

        $aggregateEvent = new AggregateEvent($aggregateValueObject1, 'testRootAggregate', 'testName');

        $aggregateEvent->setApplication($app);

        $this->assertSame('testName', $aggregateEvent->getName());

        $this->assertInstanceOf(App::class, $aggregateEvent->getApplication());

        $this->assertInstanceOf(AggregateImmutableValueObject::class, $aggregateEvent->getAggregateValueObject());

        $this->assertSame(['testkey1' => 'testvalue1'], $aggregateEvent->getAggregateValueObject()->getProperties());

        $this->assertEquals(1, $aggregateEvent->getEventType());

        $this->assertSame('testRootAggregate', $aggregateEvent->getAggregateRootName());

        $aggregateValueObject2 = new AggregateImmutableValueObject(['testkey2' => 'testvalue2']);

        $aggregateEvent->setAggregateValueObject($aggregateValueObject2);

        $this->assertInstanceOf(AggregateImmutableValueObject::class, $aggregateEvent->getAggregateValueObject());

        $this->assertSame(['testkey2' => 'testvalue2'], $aggregateEvent->getAggregateValueObject()->getProperties());
    }
}
