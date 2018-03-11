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
 * The abstract AbstractApp class is the blueprint for the MVC's main engine.
 *
 * *Description* The abstract AbstractApp class is the one that needs to be extended
 * in order to create a LightMVC AbstractApp.
 */
abstract class AbstractApp {
    
    /**@var Object:AbstractApp|null  Contains the Singleton instance of this class.*/
    protected static $appInstance;
    
    /**@var array|null  Array contains all of the AbstractApp's basic configurations.*/
    protected $baseConfig;
    
    /**@var Object:Request|null  Contains a reference to a Request instance.*/
    protected $request;
    
    /**@var Object:ServiceManager|null  Contains a reference to a ServiceManager instance.*/
    protected $serviceManager;
    
    /**@var Object:Smarty|null  Contains a reference to a Smarty instance.*/
    protected $viewObject;
    
    /**@var Object:Router|null  Contains a reference to a Router instance.*/
    protected $router;
    
    /**@var Object:Dispatcher|null  Contains a reference to a Dispatcher instance.*/
    protected $dispatcher;
    
    /**@var Object:Controller|null  Contains a reference to a polymorphic Controller instance.*/
    protected $controller;
    
    /**@var string|null  Contains a string that signifies the AbstractApp's current runlevel.*/
    protected $currentRunLevel;
    
    /**
     * Protected method : this class cannot be instantiated by the new keyword
     * because it is a Singleton.
     *
     * @param void.
     *
     * @return void.
     */
    protected abstract function __construct();
    
    /**
     * Protected method : this class cannot be copied because it is a Singleton.
     *
     * @param void.
     *
     * @return void.
     */
    protected abstract function __clone();
    
    /**
     * Static method : returns the Singleton instance of this class.
     *
     * @param void.
     *
     * @return Object:AbstractApp  Returns the current AbstractApp object.
     */
    public static function getInstance()
    {
        
    }
    
    /**
     * Initializes this class by assigning the objects and arrays
     * received in the parameters to the corresponding properties
     * and by configuring the Smarty Template Manager.  It will
     * also initialize a Doctrine Database Manager if the connection
     * parameters are given in the config/config.php file.
     *
     * @param Object:ServiceManager &$serviceManager.
     * @param Object:Smarty &$viewObject.
     * @param mixed[] &$baseConfig  Contains all of the AbstractApp's basic configurations.
     *
     * @return void.
     */
    public abstract function initialize(&$baseConfig, ServiceManager &$serviceManager = NULL, \Smarty &$viewObject = NULL);
    
    /**
     * @return the $this->baseConfig
     */
    public abstract function getBaseConfig();
    
    /**
     * @param string $name
     * @param mixed $array
     *
     * @return the $this->baseConfig
     */
    public abstract function appendBaseConfig($name, $array);
    
    /**
     * @return the $this->request
     */
    public abstract function getRequest();
    
    /**
     * @return the $this->serviceManager
     */
    public abstract function getServiceManager();

    /**
     * @param Object:ServiceManager &$serviceManager
     */
    public abstract function setServiceManager(ServiceManager &$serviceManager);

    /**
     * @return the $this->viewObject
     */
    public abstract function getViewObject();

    /**
     * @param Object:Smarty &$viewObject
     */
    public abstract function setViewObject(\Smarty &$viewObject);
    
    /**
     * @return the $this->router
     */
    public abstract function getRouter();

    /**
     * @param Object:Router &$router
     */
    public abstract function setRouter(Router &$router);

    /**
     * @return the $this->dispatcher
     */
    public abstract function getDispatcher();

    /**
     * @param Object:Dispatcher &$dispatcher
     */
    public abstract function setDispatcher(Dispatcher &$dispatcher);

    /**
     * @return the $this->controller
     */
    public abstract function getController();

    /**
     * @param Object:Controller &$controller
     */
    public abstract function setController(Controller &$controller);

    /**
     * @return the $this->currentRunLevel
     */
    public abstract function getCurrentRunLevel();
    
    /**
     * @param string $currentRunLevel
     */
    public abstract function setCurrentRunLevel($currentRunLevel);
    
    /**
     * Executes the Application's bootstrap.
     *
     * @param void.
     *
     * @return void.
     */
    public abstract function run();
    
}