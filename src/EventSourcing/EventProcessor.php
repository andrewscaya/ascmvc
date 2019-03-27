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
use Zend\EventManager\EventManager as ZendEventManager;

/**
 * Class EventProcessor
 *
 * The EventProcessor class processes events by triggering any EventListener classes.
 *
 */
class EventProcessor extends ZendEventManager
{

}
