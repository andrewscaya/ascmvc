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
 * The abstract AbstractApp class is the blueprint for the MVC's main engine.
 *
 * The abstract AbstractApp class is the one that needs to be extended
 * in order to create a LightMVC AbstractApp.
 */
abstract class AbstractApp {
    
    /**
     * Contains the Singleton instance of this class.
     *
     * @var AbstractApp|null
     */
    protected static $appInstance;
    
    /**
     * Array contains all of the AbstractApp's basic configurations.
     *
     * @var array|null
     */
    protected $baseConfig;
    
    /**
     * Contains a reference to a Request instance.
     *
     * @var AbstractRequest|null
     */
    protected $request;
    
    /**
     * Contains a reference to a ServiceManager instance.
     *
     * @var AbstractServiceManager|null
     */
    protected $serviceManager;
    
    /**
     * Contains a reference to a Smarty instance.
     *
     * @var AbstractViewObject|null
     */
    protected $viewObject;
    
    /**
     * Contains a reference to a Router instance.
     *
     * @var AbstractRouter|null
     */
    protected $router;
    
    /**
     * Contains a reference to a Dispatcher instance.
     *
     * @var AbstractDispatcher|null
     */
    protected $dispatcher;
    
    /**
     * Contains a reference to a polymorphic Controller instance.
     *
     * @var AbstractController|null
     */
    protected $controller;
    
    /**
     * Contains a string that signifies the AbstractApp's current runlevel.
     *
     * @var string|null
     */
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
     * @return AbstractApp
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
     * @param array &$baseConfig  Contains all of the AbstractApp's basic configurations
     * @param AbstractServiceManager &$serviceManager | NULL
     * @param AbstractViewObject &$viewObject
     *
     * @return array.
     */
    public abstract function initialize(Array &$baseConfig, AbstractServiceManager &$serviceManager = NULL, \Smarty &$viewObject = NULL);
    
    /**
     * Get the application's base configuration.
     *
     * @return array
     */
    public abstract function getBaseConfig();
    
    /**
     * Modify the application's base configuration.
     *
     * @param string $name
     * @param array $array
     *
     * @return AbstractApp
     */
    public abstract function appendBaseConfig($name, $array);
    
    /**
     * Get the AbstractRequest object.
     *
     * @return AbstractRequest
     */
    public abstract function getRequest();
    
    /**
     * Get the AbstractServiceManager object.
     *
     * @return AbstractServiceManager
     */
    public abstract function getServiceManager();

    /**
     * Set the AbstractServiceManager object.
     *
     * @param AbstractServiceManager
     *
     * @return AbstractApp
     */
    public abstract function setServiceManager(AbstractServiceManager &$serviceManager);

    /**
     * Get the AbstractViewObject object.
     *
     * @return AbstractViewObject
     */
    public abstract function getViewObject();

    /**
     * Set the AbstractViewObject object.
     *
     * @param AbstractViewObject
     *
     * @return AbstractApp
     */
    public abstract function setViewObject(AbstractViewObject &$viewObject);
    
    /**
     * Get the AbstractRouter object.
     *
     * @return AbstractRouter
     */
    public abstract function getRouter();

    /**
     * Set the AbstractRouter object.
     *
     * @param AbstractRouter
     *
     * @return AbstractApp
     */
    public abstract function setRouter(AbstractRouter &$router);

    /**
     * Get the AbstractDispatcher object.
     *
     * @return AbstractDispatcher
     */
    public abstract function getDispatcher();

    /**
     * Set the AbstractDispatcher object.
     *
     * @param AbstractDispatcher
     *
     * @return AbstractApp
     */
    public abstract function setDispatcher(AbstractDispatcher &$dispatcher);

    /**
     * Get the AbstractController object.
     *
     * @return AbstractController
     */
    public abstract function getController();

    /**
     * Set the AbstractController object.
     *
     * @param AbstractController
     *
     * @return AbstractApp
     */
    public abstract function setController(AbstractController &$controller);

    /**
     * Get the application's current runlevel.
     *
     * @return string
     */
    public abstract function getCurrentRunLevel();
    
    /**
     * Set the application's current runlevel.
     *
     * @param string $currentRunLevel
     *
     * @return AbstractApp
     */
    public abstract function setCurrentRunLevel($currentRunLevel);
    
    /**
     * Executes the Application's bootstrap events.
     *
     * @param void.
     *
     * @return void.
     */
    public abstract function run();
    
}