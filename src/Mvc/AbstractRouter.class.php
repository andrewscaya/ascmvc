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
 * The AbstractRouter class is the blueprint for the MVC's main router.
 *
 * *Description* The AbstractRouter class is the one that needs to be extended
 * in order to create a LightMVC router.
 */
abstract class AbstractRouter {
    
    /**@var Object:App|null  Contains a reference to the Singleton instance of the App class.*/
    protected $app;
    
    /**@var array|null  Array contains all of the app's basic configurations.*/
    protected $baseConfig;
    
    /**@var array|null  Contains an associative array of all of the URI's elements.*/
    protected $currentRequestURI;
    
    /**@var string|null  Contains a string that is the name of the requested handler.*/
    protected $controllerName;
    
    /**@var string|null  Contains a string that is the name of the controller's requested method.*/
    protected $controllerMethodName;
    
    /**@var Object:ReflectionClass|null  Contains a ReflectionClass instance.*/
    protected $controllerReflection;
    
    /**@var string|null  Contains a string that is the URI of the controller's file that must be included.*/
    protected $controllerFile;
    
    
    /**
     * Initializes this class by assigning the objects contained in the
     * referenced App object to the corresponding properties.
     *
     * @param Object:App &$app.
     *
     * @return void.
     */
    public abstract function __construct(App &$app);
    
    /**
     * @return array|null $this->currentRequestURI
     */
    public abstract function getCurrentRequestURI();
    
    /**
     * @return Object:Controller|null $this->controllerReflection
     */
    public abstract function getControllerReflection();
    
    /**
     * @return string|null $this->controllerName
     */
    public abstract function getControllerName();
    
    /**
     * @return string|null $this->controllerMethodName
     */
    public abstract function getControllerMethodName();
    
    /**
     * @return string|null $this->controllerFile
     */
    public abstract function getControllerFile();
    
}