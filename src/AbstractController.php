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
 * The abstract AbstractController class is the blueprint for the MVC's controllers.
 *
 * *Description* The AbstractController class is the one that needs to be extended
 * in order to create a LightMVC controller.
 */
abstract class AbstractController implements AscmvcEventManagerListenerInterface {
    
    /**
     * Contains a reference to the array containing some of the app's basic configurations.
     *
     * @var Array|null
     */
    protected $baseConfig;
    
    /**
     * Array contains all of the values that will be assigned to the controller's view manager.
     *
     * @var array|null
     */
    protected $view;
    
    /**
     * Array contains the controller's helper objects.
     *
     * @var array|null
     */
    protected $helpers;
    
    /**
     * Initializes this class by assigning the objects and arrays
     * contained in the referenced App object to the corresponding
     * properties.
     *
     * @param array $baseConfig
     * 
     * @return void.
     */
    public abstract function __construct(array $baseConfig);
    
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
    
    /**
     * Method corresponding to the controller's default action.
     *
     * @param void.
     *
     * @return mixed.
     */
    public abstract function indexAction($vars = null);

}
