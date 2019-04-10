<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.3
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
interface AscmvcDispatchListenerInterface
{

    /**
     * Allows an implementing object to interrupt the application's runtime after the
     * registration of the controller object, but before the controller manager's call
     * to the controller's action method.  This phase allows for controller specific
     * configuration.
     *
     * @param AscmvcEvent $event
     *
     */
    public function onDispatch(AscmvcEvent $event);
}
