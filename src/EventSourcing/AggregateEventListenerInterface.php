<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.1.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.1.0
 */

namespace Ascmvc\EventSourcing;

use Ascmvc\EventSourcing\Event\AggregateEvent;

/**
 * Interface EventListenerInterface
 *
 * @package Ascmvc\EventSourcing
 */
interface AggregateEventListenerInterface extends EventListenerInterface
{
    /**
     * Aggregate Event listener method.
     *
     * @param AggregateEvent $event
     * @return mixed
     */
    public function onAggregateEvent(AggregateEvent $event);
}
