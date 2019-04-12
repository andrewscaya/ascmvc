<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.0.0
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
     * Policy constructor.
     *
     * @param EventDispatcher $eventDispatcher
     */
    protected function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
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
     * Event listener.
     *
     * @param Event $event
     *
     * @return mixed
     */
    public function onEvent(Event $event)
    {
    }
}