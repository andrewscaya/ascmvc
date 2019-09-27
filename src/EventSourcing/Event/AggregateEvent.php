<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.2
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.0.0
 */

namespace Ascmvc\EventSourcing\Event;

use Ascmvc\EventSourcing\AggregateImmutableValueObject;

/**
 * Class AggregateEvent
 *
 * @package Ascmvc\EventSourcing\Event
 */
class AggregateEvent extends Event
{
    /**
     * Type of event.
     *
     * 1: Regular event
     * 4: Read event
     * 6: Write event
     */
    const TYPE = 1;

    /**
     * Contains an instance of AggregateImmutableValueObject.
     *
     * @var AggregateImmutableValueObject
     */
    protected $aggregateValueObject;

    /**
     * Contains the name of the bounded context (event sourcing aggregate).
     *
     * @var null|string
     */
    protected $aggregateRootName;

    /**
     * AggregateEvent constructor.
     *
     * @param AggregateImmutableValueObject $aggregateValueObject
     * @param null $aggregateRootName
     * @param null $name
     * @param null $target
     * @param null $params
     */
    public function __construct(AggregateImmutableValueObject $aggregateValueObject, $aggregateRootName = null, $name = null, $target = null, $params = null)
    {
        parent::__construct($name, $target, $params);

        $this->aggregateValueObject = $aggregateValueObject;

        if (!is_null($aggregateRootName)) {
            $this->aggregateRootName = $aggregateRootName;
        }
    }

    /**
     * Gets the event type.
     *
     * @return int
     */
    public function getEventType()
    {
        return self::TYPE;
    }

    /**
     * Gets the event's immutable value object.
     *
     * @return AggregateImmutableValueObject
     */
    public function getAggregateValueObject(): AggregateImmutableValueObject
    {
        return $this->aggregateValueObject;
    }

    /**
     * Sets the event's immutable value object.
     *
     * @param AggregateImmutableValueObject $aggregateValueObject
     * @return AggregateEvent
     */
    public function setAggregateValueObject(AggregateImmutableValueObject $aggregateValueObject): AggregateEvent
    {
        $this->aggregateValueObject = $aggregateValueObject;

        return $this;
    }

    // @codeCoverageIgnoreStart
    /**
     * DEPRECATED - Use getAggregateRootName() instead - Will be removed in 4.0.0.
     *
     * Gets the name of the aggregate root that created the event.
     *
     * @return null|string
     */
    public function getRootAggregateName()
    {
        return $this->aggregateRootName;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Gets the name of the aggregate root that created the event.
     *
     * @return null|string
     */
    public function getAggregateRootName()
    {
        return $this->aggregateRootName;
    }
}
