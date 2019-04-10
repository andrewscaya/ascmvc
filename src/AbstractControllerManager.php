<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.3
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      1.0.0
 */

namespace Ascmvc;

/**
 * The abstract AbstractControllerManager class is the blueprint for the MVC's ControllerManager.
 *
 * The AbstractControllerManager class is the one that needs to be extended
 * in order to create a LightMVC ControllerManager.
 */
abstract class AbstractControllerManager
{

    /**
     * Contains a reference to the Singleton instance of the AbstractApp class.
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
     * Contains the name of the AbstractController instance.
     *
     * @var string
     */
    protected $controllerName;

    /**
     * Contains the name of the AbstractController's method.
     *
     * @var string
     */
    protected $controllerMethodName;

    /**
     * Contains a reflection of the AbstractController instance.
     *
     * @var \ReflectionClass
     */
    protected $controllerReflection;

    /**
     * Contains the controller's filename.
     *
     * @var string
     */
    protected $controllerFileName;

    /**
     * Contains a reference to a AbstractController instance.
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
     * Array containing the values of the request (SERVER, REQUEST, GET, POST, PUT, PATCH, DELETE).
     *
     * @var array|null
     */
    protected $vars;

    /**
     * Initializes this class by assigning the objects contained in the
     * referenced application object to the corresponding properties.
     *
     * @param AbstractApp &$app
     * @param string $controllerName
     * @param array $vars
     *
     * @return void
     */
    public abstract function __construct(AbstractApp &$app, $controllerName, array $vars = []);

    /**
     * Method that calls the MVC's appropriate request handler.
     *
     * @param void
     *
     * @return void
     */
    public abstract function execute();
}
