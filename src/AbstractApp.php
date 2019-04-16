<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      1.0.0
 */

namespace Ascmvc;

use Ascmvc\Mvc\AscmvcEvent;
use Ascmvc\Mvc\AscmvcEventManager;
use Ascmvc\Session\SessionManager;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

/**
 * Class AbstractApp
 *
 * The abstract AbstractApp class is the blueprint for the MVC's main engine.
 *
 * @package Ascmvc
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
     * @var Request|null
     */
    protected $request;

    /**
     * Contains a reference to a Response instance.
     *
     * @var Response|null
     */
    protected $response;

    /**
     * Contains a reference to the SessionManager instance.
     *
     * @var SessionManager|null
     */
    protected $sessionManager;

    /**
     * Contains a reference to a \Pimple\Container instance.
     *
     * @var \Pimple\Container|null
     */
    protected $serviceManager;

    /**
     * Contains a reference to the AscmvcEventManager instance.
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
     * Contains a reference to an AbstractRouter instance.
     *
     * @var AbstractRouter|null
     */
    protected $router;

    /**
     * Contains a reference to a AbstractControllerManager instance.
     *
     * @var AbstractControllerManager|null
     */
    protected $controllerManager;

    /**
     * Contains a reference to a AbstractController instance.
     *
     * @var AbstractController|null
     */
    protected $controller;

    /**
     * Contains the controller's output.
     *
     * @var Response|array|string|null
     */
    protected $controllerOutput;


    /**
     * This class should be a Singleton, but instantiation is needed for compatibility with Swoole.
     *
     * @param void.
     *
     * @return void.
     */
    public abstract function __construct();

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
     * Builds the baseConfig array from the various configuration files.
     *
     * @return array
     */
    public abstract function boot();

    /**
     * Initializes the application with the parameters that
     * are given in the baseConfig array.
     *
     * @param array &$baseConfig
     *
     * @return AbstractApp
     */
    public abstract function initialize(array &$baseConfig);

    /**
     * Sends the final response to the output buffer.
     *
     * @param Response $response
     *
     * @return void
     */
    public abstract function display(Response $response);

    /**
     * Renders the response.
     *
     * @param Response|array|string $controllerOutput
     *
     * @return Response
     */
    public abstract function render($controllerOutput);

    /**
     * The application's main runtime method. It executes the Application's bootstrap events.
     *
     * @param void
     *
     * @return void
     */
    public abstract function run();

    /**
     * Gets the application's base configuration.
     *
     * @param void
     *
     * @return array
     */
    public abstract function getBaseConfig();

    /**
     * Gets what is useful to the controllers from the application's base configuration.
     *
     * @param void
     *
     * @return array
     */
    public abstract function getBaseConfigForControllers();

    /**
     * Adds an element to the application's base configuration.
     *
     * @param string $name
     * @param array $array
     *
     * @return AbstractApp
     */
    public abstract function appendBaseConfig($name, $array);

    /**
     * Gets the ServerRequestInterface object.
     *
     * @return ServerRequestInterface
     */
    public abstract function getRequest();

    /**
     * Sets the ServerRequestInterface object.
     *
     * @param ServerRequestInterface
     *
     * @return ServerRequestInterface
     */
    public abstract function setRequest(ServerRequestInterface $request);

    /**
     * Gets the Response object.
     *
     * @return ResponseInterface
     */
    public abstract function getResponse();

    /**
     * Sets the Response object.
     *
     * @param ResponseInterface
     *
     * @return ResponseInterface
     */
    public abstract function setResponse(ResponseInterface $response);

    /**
     * Gets the SessionManager.
     *
     * @return SessionManager|null
     */
    public abstract function getSessionManager();

    /**
     * Sets the SessionManager.
     *
     * @param SessionManager $sessionManager
     * @return SessionManager|null
     */
    public abstract function setSessionManager(SessionManager $sessionManager);

    /**
     * Gets the Pimple\Container object.
     *
     * @return \Pimple\Container
     */
    public abstract function getServiceManager();

    /**
     * Sets the Pimple\Container object.
     *
     * @param \Pimple\Container
     *
     * @return AbstractApp
     */
    public abstract function setServiceManager(Container &$serviceManager);

    /**
     * Gets the AscmvcEventManager object.
     *
     * @return AscmvcEventManager
     */
    public abstract function getEventManager();

    /**
     * Sets the AscmvcEventManager object.
     *
     * @param AscmvcEventManager
     *
     * @return AbstractApp
     */
    public abstract function setEventManager(AscmvcEventManager &$eventManager);

    /**
     * Gets the AscmvcEvent object.
     *
     * @return AscmvcEvent
     */
    public abstract function getEvent();

    /**
     * Sets the AscmvcEvent object.
     *
     * @param AscmvcEvent
     *
     * @return AbstractApp
     */
    public abstract function setEvent(AscmvcEvent &$event);

    /**
     * Gets the AbstractRouter object.
     *
     * @return AbstractRouter
     */
    public abstract function getRouter();

    /**
     * Sets the AbstractRouter object.
     *
     * @param AbstractRouter
     *
     * @return AbstractApp
     */
    public abstract function setRouter(AbstractRouter &$router);

    /**
     * Gets the AbstractControllerManager object.
     *
     * @return AbstractControllerManager
     */
    public abstract function getControllerManager();

    /**
     * Sets the AbstractControllerManager object.
     *
     * @param AbstractControllerManager
     *
     * @return AbstractApp
     */
    public abstract function setControllerManager(AbstractControllerManager &$controllerManager);

    /**
     * Gets the AbstractController object.
     *
     * @return AbstractController|null
     */
    public abstract function getController();

    /**
     * Sets the AbstractController object.
     *
     * @param AbstractController
     *
     * @return AbstractApp
     */
    public abstract function setController(AbstractController &$controller);

    /**
     * Gets the Controller's output.
     *
     * @return Response|array|string|null
     */
    public abstract function getControllerOutput();

    /**
     * Sets the Controller's output.
     *
     * @param array $controllerOutput
     *
     * @return AbstractApp
     */
    public abstract function setControllerOutput($controllerOutput);

    /**
     * Gets the Template Manager object.
     *
     * @return \League\Plates\Engine|\Smarty|\Twig_Environment
     */
    public abstract function getViewObject();

    /**
     * Sets the Template Manager object.
     *
     * @param \League\Plates\Engine|\Smarty|\Twig_Environment
     *
     * @return AbstractApp
     */
    public abstract function setViewObject(&$viewObject);
}
