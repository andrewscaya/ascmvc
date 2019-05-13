<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.1.2
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      3.0.0
 */

namespace AscmvcTest\EventSourcing\Event;

use Ascmvc\EventSourcing\AggregateImmutableValueObject;
use Ascmvc\EventSourcing\Event\AggregateEvent;
use Ascmvc\EventSourcing\Event\ReadAggregateCompletedEvent;
use Ascmvc\EventSourcing\Event\WriteAggregateCompletedEvent;
use Ascmvc\Mvc\App;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class WriteAggregateCompletedEventTest extends TestCase
{
    public function testCreateWriteAggregateCompletedInstance()
    {
        $app = App::getInstance();

        $aggregateValueObject = new AggregateImmutableValueObject(['testkey' => 'testvalue']);

        $aggregateEvent = new WriteAggregateCompletedEvent($aggregateValueObject, 'testRootAggregate', 'testName');

        $aggregateEvent->setApplication($app);

        $this->assertSame('testName', $aggregateEvent->getName());

        $this->assertInstanceOf(App::class, $aggregateEvent->getApplication());

        $this->assertInstanceOf(AggregateImmutableValueObject::class, $aggregateEvent->getAggregateValueObject());

        $this->assertEquals(6, $aggregateEvent->getEventType());

        $this->assertSame('testRootAggregate', $aggregateEvent->getRootAggregateName());
    }
}
