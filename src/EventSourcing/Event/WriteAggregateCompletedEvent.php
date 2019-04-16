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

namespace Ascmvc\EventSourcing\Event;

/**
 * Class WriteAggregateCompletedEvent
 *
 * @package Ascmvc\EventSourcing\Event
 */
class WriteAggregateCompletedEvent extends AggregateEvent
{
    /**
     * Type of event.
     *
     * 1: Regular event
     * 4: Read event
     * 6: Write event
     */
    const TYPE = 6;

    /**
     * Gets the event type.
     *
     * @return int
     */
    public function getEventType()
    {
        return self::TYPE;
    }
}
