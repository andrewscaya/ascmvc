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
abstract class AbstractController implements EventManagerListenerInterface {
    
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
     * Configure the application.
     *
     * @param array $baseConfig
     *
     * @return mixed.
     */
    public function config(array $baseConfig)
    {
    
    }
    
    /**
     * Method corresponding to the controller's default action.
     *
     * @param void.
     *
     * @return mixed.
     */
    public abstract function indexAction($vars = null);

}
