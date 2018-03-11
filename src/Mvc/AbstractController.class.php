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

namespace Ascmvc\Mvc;


/**
 * The abstract AbstractController class is the blueprint for the MVC's controllers.
 *
 * *Description* The AbstractController class is the one that needs to be extended
 * in order to create a LightMVC controller.
 */
abstract class AbstractController implements ServiceManagerListenerInterface {
    
    /**@var Object:App|null  Contains a reference to the Singleton instance of the App class.*/
    protected $app;
    
    /**@var array|null  Contains a reference to the array containing all of the app's basic configurations.*/
    protected $baseConfig;
    
    /**@var Object:Smarty|null  Contains a reference to a Smarty instance.*/
    protected $viewObject;
    
    /**@var array|null  Array contains all of the values that will be assigned to the controller's view manager.*/
    protected $view;
    
    /**@var Object:ServiceManager|null  Contains a reference to a ServiceManager instance.*/
    protected $serviceManager;
    
    /**@var array|null  Array contains the controller's helper objects.*/
    protected $helpers;
    
    /**
     * Initializes this class by assigning the objects and arrays
     * contained in the referenced App object to the corresponding
     * properties.
     *
     * @param Object:App &$app.
     * 
     * @return void.
     */
    public abstract function __construct(App &$app);
    
    public static function config(App &$app)
    {
    
    }
    
    /**
     * Method corresponding to the controller's default action.
     *
     * @param void.
     *
     * @return void.
     */
    public abstract function indexAction();

}