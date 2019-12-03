<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.3.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.0.0
 */

namespace Ascmvc\EventSourcing;

use Ascmvc\EventSourcing\Event\Event;

/**
 * Class EventListener
 *
 * @package Ascmvc\EventSourcing
 */
class EventListener implements EventListenerInterface
{
    /**
     * Contains an instance of the \Ascmvc\EventSourcing\EventDispatcher
     *
     * @var EventDispatcher
     */
    protected $eventDispatcher;

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
     * @param Event $event
     * @return \Generator
     */
    public function __invoke(Event $event)
    {
        yield $this->onEvent($event);
    }

    /**
     * Returns an instance of this class.
     *
     * @param EventDispatcher $eventDispatcher
     *
     * @return EventListener
     */
    public static function getInstance(EventDispatcher $eventDispatcher)
    {
        return new self($eventDispatcher);
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
