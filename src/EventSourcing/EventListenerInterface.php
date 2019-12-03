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
 * Interface EventListenerInterface
 *
 * @package Ascmvc\EventSourcing
 */
interface EventListenerInterface
{
    /**
     * Event listener method.
     *
     * @param Event $event
     */
    public function onEvent(Event $event);
}
