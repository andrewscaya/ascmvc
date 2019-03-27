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

use Zend\EventManager\EventInterface;

/**
 * Interface EventListenerInterface
 *
 * The EventListenerInterface interface is implemented by any Policy class that
 * wishes to be triggered by the EventProcessor.
 *
 */
interface EventListenerInterface
{
    public function onEvent(EventInterface $event);
}
