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
abstract class AbstractController implements ServiceManagerListenerInterface {
    
    /**
     * Contains a reference to the Singleton instance of the App class.
     *
     * @var AbstractApp|null
     */
    protected $app;
    
    /**
     * Contains a reference to the array containing all of the app's basic configurations.
     *
     * @var Array|null
     */
    protected $baseConfig;
    
    /**
     * Contains a reference to a Smarty instance.
     *
     * @var AbstractViewObject|null
     */
    protected $viewObject;
    
    /**
     * Array contains all of the values that will be assigned to the controller's view manager.
     *
     * @var array|null
     */
    protected $view;
    
    /**
     * Contains a reference to a ServiceManager instance.
     *
     * @var AbstractServiceManager|null
     */
    protected $serviceManager;
    
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
     * @param AbstractApp &$app
     * 
     * @return void.
     */
    public abstract function __construct(AbstractApp &$app);

    /**
     * Configure the application.
     *
     * @param AbstractApp &$app
     *
     * @return mixed.
     */
    public static function config(AbstractApp &$app)
    {
    
    }
    
    /**
     * Method corresponding to the controller's default action.
     *
     * @param void.
     *
     * @return mixed.
     */
    public abstract function indexAction();

}