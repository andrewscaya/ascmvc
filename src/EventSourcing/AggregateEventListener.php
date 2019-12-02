<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.3
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.1.0
 */

namespace Ascmvc\EventSourcing;

use Ascmvc\EventSourcing\Event\AggregateEvent;
use Ascmvc\EventSourcing\Event\Event;

/**
 * Class AggregateEventListener
 *
 * @package Ascmvc\EventSourcing
 */
class AggregateEventListener implements AggregateEventListenerInterface
{
    /**
     * Contains an instance of the \Ascmvc\EventSourcing\EventDispatcher
     *
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * Contains the name of the aggregate root that created the event.
     *
     * @var string
     */
    protected $aggregateRootName;

    /**
     * EventListener constructor.
     *
     * @param EventDispatcher $eventDispatcher
     */
    protected function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Runs the EventListener class as a function.
     *
     * @param AggregateEvent $event
     * @return \Generator
     */
    public function __invoke(AggregateEvent $event)
    {
        yield $this->onAggregateEvent($event);
    }

    /**
     * Returns an instance of this class.
     *
     * @param EventDispatcher $eventDispatcher
     *
     * @return AggregateEventListener
     */
    public static function getInstance(EventDispatcher $eventDispatcher)
    {
        return new self($eventDispatcher);
    }

    /**
     * Aggregate Event listener method.
     *
     * @param AggregateEvent $event
     * @return \Generator
     */
    public function onAggregateEvent(AggregateEvent $event)
    {
        if (!isset($this->aggregateRootName)) {
            $this->aggregateRootName = $event->getAggregateRootName();
        }

        yield $this->onEvent($event);
    }

    /**
     * Event listener method.
     *
     * @param Event $event
     * @return \Generator
     */
    public function onEvent(Event $event)
    {
        yield;
    }
}
