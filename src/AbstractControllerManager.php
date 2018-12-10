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
 * The abstract AbstractControllerManager class is the blueprint for the MVC's ControllerManagers.
 *
 * The AbstractControllerManager class is the one that needs to be extended
 * in order to create a LightMVC ControllerManager.
 */
abstract class AbstractControllerManager {

    /**
     * Contains a reference to the Singleton instance of the App class.
     *
     * @var AbstractApp|null
     */
    protected $app;
    
    /**
     * Contains an associative array of all of the URI's elements.
     *
     * @var array|null
     */
    protected $currentRequestURI;
    
    /**
     * Contains the name of the polymorphic Controller instance.
     *
     * @var string
     */
    protected $controllerName;
    
    /**
     * Contains the name of the Controller's method.
     *
     * @var string
     */
    protected $controllerMethodName;
    
    /**
     * Contains a reflection of the polymorphic Controller instance.
     *
     * @var \ReflectionClass
     */
    protected $controllerReflection;
    
    /**
     * Contains the Controller's filename.
     *
     * @var string
     */
    protected $controllerFileName;

    /**
     * Contains a reference to a polymorphic Controller instance.
     *
     * @var AbstractController|null
     */
    protected $controller;

    /**
     * Contains a string that is the name of the controller's method that can handle the dispatch.
     *
     * @var string|null
     */
    protected $method;
    
    /**
     * Array containing the values of the request (GET, POST, PUT, PATCH, DELETE).
     *
     * @var string|null
     */
    protected $vars;

    /**
     * Initializes this class by assigning the objects contained in the
     * referenced App object's router object to the corresponding properties.
     *
     * @param AbstractApp &$app.
     * @param string $controllerName.
     * @param array $vars.
     *
     * @return void.
     */
    public abstract function __construct(AbstractApp &$app, $controllerName, array $vars = []);
    
    /**
     * Method that calls the MVC's appropriate request handler.
     *
     * @param void.
     *
     * @return void.
     */
    public abstract function execute();

}
