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

use Ascmvc\EventSourcing\AggregateImmutableValueObject;
use Ascmvc\EventSourcing\Event\AggregateEvent;
use Ascmvc\EventSourcing\EventDispatcher;
use Ascmvc\Mvc\App;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class EventDispatcherTest extends TestCase
{
    public function testCreateEventDispatcherInstance()
    {
        $app = App::getInstance();

        $eventDispatcher = new EventDispatcher($app);

        $aggregateValueObject = new AggregateImmutableValueObject(['testkey' => 'testvalue']);

        $aggregateEvent = new AggregateEvent($aggregateValueObject, 'testRootAggregate', 'testName');

        $eventDispatcher->dispatch($aggregateEvent);

        $this->assertInstanceOf(App::class, $aggregateEvent->getApplication());
    }
}
