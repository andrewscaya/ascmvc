<?php
/**
 * ASC LightMVC
 *
 * @package    ASC LightMVC
 * @author     Andrew Caya
 * @link       https://github.com/andrewscaya
 * @version    1.0.0
 * @license    http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Ascmvc;


/**
 * EventManagerListenerInterface allows the implementing class
 * to be consumed as a AscmvcEventManager class listener.
 * 
 * The interface's methods correspond exactly to the
 * App Class' runlevels as they are defined in its run() method
 * so that, in turn, these methods may be dynamically called by the
 * EventManager's event-driven triggerEvent() method.
 */
interface AscmvcEventManagerListenerInterface {
    
    /**
     * Allows an implementing object to interrupt the App's runtime before
     * the instantiation of the Router, Dispatcher and Controller classes.
     *
     * @param AscmvcEvent $event
     *
     * @return void
     */
    public static function onBootstrap(AscmvcEvent $event);
    
    /**
     * Allows an implementing object to interrupt the App's runtime after the
     * the Event Manager's registration of the Controller object but before
     * the Dispatcher's call to the Controller's action method.  This phase allows
     * for Controller configuration.
     *
     * @param AscmvcEvent $event
     *
     * @return void
     */
    public function onDispatch(AscmvcEvent $event);
    
    /**
     * Allows an implementing object to interrupt the App's runtime before it
     * sends the Controller's final response to the client.
     *
     * @param AscmvcEvent $event
     *
     * @return void
     */
    public function onRender(AscmvcEvent $event);
    
}
