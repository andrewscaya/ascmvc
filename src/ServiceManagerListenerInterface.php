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
 * ServiceManagerListenerInterface allows the implementing class
 * to be consumed as a ServiceManager class listener.
 * 
 * The interface's methods correspond exactly to the
 * App Class' runlevels as they are defined in its run() method
 * so that, in turn, these methods may be dynamically called by the
 * Service Manager's event-driven processEvents() method.
 */
interface ServiceManagerListenerInterface {
    
    /**
     * Allows an implementing object to interrupt the App's runtime before
     * the instantiation of the Router, Dispatcher and Controller classes.
     *
     * @param AbstractApp &$app
     *
     * @return void
     */
    public static function preboot(AbstractApp &$app);
    
    /**
     * Allows an implementing object to interrupt the App's runtime immediately
     * after the instantiation of the Router, Dispatcher and Controller classes
     * but before the Service Manager's registration of the Controller object.
     *
     * @param AbstractApp &$app
     *
     * @return void
     */
    public static function postboot(AbstractApp &$app);
    
    /**
     * Allows an implementing object to interrupt the App's runtime after the
     * the Service Manager's registration of the Controller object but before
     * the Dispatcher's call to the Controller's action method.  This phase allows
     * for Controller configuration.  By default, predispatch() calls the Controller's
     * config() method.
     *
     * @param void
     *
     * @return void
     */
    public function predispatch();
    
    /**
     * Allows an implementing object to interrupt the App's runtime after the 
     * completion of the Dispatcher's call to the Controller's action method and
     * the rendering of this method's frontend.
     *
     * @param void
     *
     * @return void
     */
    public function postdispatch();
    
    /**
     * Allows an implementing object to interrupt the App's runtime before it
     * sends the Controller's final response to the client.
     *
     * @param void
     *
     * @return void
     */
    public function preresponse();
    
}