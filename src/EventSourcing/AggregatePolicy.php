<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.1.0
 */

namespace Ascmvc\EventSourcing;

/**
 * Class AggregatePolicy
 *
 * @package Ascmvc\EventSourcing
 */
class AggregatePolicy extends AggregateEventListener
{
    /**
     * Returns an instance of this class.
     *
     * @param EventDispatcher $eventDispatcher
     *
     * @return AggregatePolicy
     */
    public static function getInstance(EventDispatcher $eventDispatcher)
    {
        return new self($eventDispatcher);
    }
}
