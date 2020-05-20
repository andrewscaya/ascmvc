<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    4.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace Ascmvc;

use Ascmvc\Mvc\AscmvcEvent;

/**
 * AscmvcRenderListenerInterface allows the implementing class
 * to be consumed as a AscmvcEventManager class listener.
 *
 * The interface's methods correspond exactly to the
 * application's events as they are used in its run() method
 * so that, in turn, these methods may be dynamically called by the
 * EventManager's event-driven "trigger" methods.
 */
interface AscmvcBootstrapListenerInterface
{

    /**
     * Allows an implementing object to interrupt the application's runtime before
     * the instantiation of the Router, Dispatcher and Controller classes.
     *
     * @param AscmvcEvent $event
     *
     */
    public static function onBootstrap(AscmvcEvent $event);
}
