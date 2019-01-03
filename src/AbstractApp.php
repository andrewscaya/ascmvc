<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.0
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      1.0.0
 */

namespace Ascmvc;

use Ascmvc\Mvc\AscmvcEvent;
use Ascmvc\Mvc\AscmvcEventManager;
use Pimple\Container;
use Zend\Diactoros\Response;

/**
 * The abstract AbstractApp class is the blueprint for the MVC's main engine.
 *
 * The abstract AbstractApp class is the one that needs to be extended
 * in order to create a LightMVC AbstractApp.
 */
abstract class AbstractApp
{

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
     * @var Zend\Diactoros\Request|null
     */
    protected $request;

    /**
     * Contains a reference to a Response instance.
     *
     * @var Zend\Diactoros\Request|null
     */
    protected $response;

    /**
     * Contains a reference to a Pimple\Container instance.
     *
     * @var Pimple\Container|null
     */
    protected $serviceManager;

    /**
     * Contains a reference to the EventManager instance.
     *
     * @var AscmvcEventManager|null
     */
    protected $eventManager;

    /**
     * Contains a reference to the AscmvcEvent instance.
     *
     * @var AscmvcEvent|null
     */
    protected $event;

    /**
     * Contains a reference to a Template Manager instance.
     *
     * @var Object|null
     */
    protected $viewObject;

    /**
     * Contains a reference to a Router instance.
     *
     * @var AbstractRouter|null
     */
    protected $router;

    /**
     * Contains a reference to a ControllerManager instance.
     *
     * @var AbstractControllerManager|null
     */
    protected $controllerManager;

    /**
     * Contains a reference to a polymorphic Controller instance.
     *
     * @var AbstractController|null
     */
    protected $controller;


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
     * Initializes the application with the parameters that
     * are given in the config/config.php file.
     *
     * @param array &$baseConfig  Contains all of the AbstractApp's basic configurations
     *
     * @return array.
     */
    public abstract function initialize(array &$baseConfig);

    /**
     * Sends the final response to the output buffer.
     *
     * @param Zend\Diactoros\Response $response.
     *
     * @return void
     */
    public abstract function display(Response $response);

    /**
     * Executes the Application's bootstrap events.
     *
     * @param mixed $controllerOutput
     *
     * @return Zend\Diactoros\Response $response
     */
    public abstract function render($controllerOutput);

    /**
     * Executes the Application's bootstrap events.
     *
     * @param void
     *
     * @return void
     */
    public abstract function run();

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
     * @return Zend\Diactoros\Request
     */
    public abstract function getRequest();

    /**
     * Get the AbstractResponse object.
     *
     * @return Zend\Diactoros\Response
     */
    public abstract function getResponse();

    /**
     * Set the AbstractResponse object.
     *
     * @param Zend\Diactoros\Response
     *
     * @return Zend\Diactoros\Response
     */
    public abstract function setResponse(Response $response);

    /**
     * Get the Pimple\Container object.
     *
     * @return Pimple\Container
     */
    public abstract function getServiceManager();

    /**
     * Set the Pimple\Container object.
     *
     * @param Pimple\Container
     *
     * @return AbstractApp
     */
    public abstract function setServiceManager(Container &$serviceManager);

    /**
     * Get the AscmvcEventManager object.
     *
     * @return AscmvcEventManager
     */
    public abstract function getEventManager();

    /**
     * Set the AscmvcEventManager object.
     *
     * @param AscmvcEventManager
     *
     * @return AbstractApp
     */
    public abstract function setEventManager(AscmvcEventManager &$eventManager);

    /**
     * Get the AscmvcEvent object.
     *
     * @return AscmvcEvent
     */
    public abstract function getEvent();

    /**
     * Set the AscmvcEvent object.
     *
     * @param AscmvcEvent
     *
     * @return AscmvcEvent
     */
    public abstract function setEvent(AscmvcEvent &$event);

    /**
     * Get the AbstractViewObject object.
     *
     * @return Object
     */
    public abstract function getViewObject();

    /**
     * Set the View object.
     *
     * @param Object
     *
     * @return AbstractApp
     */
    public abstract function setViewObject(&$viewObject);

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
     * Get the AbstractControllerManager object.
     *
     * @return AbstractControllerManager
     */
    public abstract function getControllerManager();

    /**
     * Set the AbstractControllerManager object.
     *
     * @param AbstractControllerManager
     *
     * @return AbstractApp
     */
    public abstract function setControllerManager(AbstractControllerManager &$controllerManager);

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
}
