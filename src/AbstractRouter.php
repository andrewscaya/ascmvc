<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    4.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      1.0.0
 */

namespace Ascmvc;

use Ascmvc\Mvc\AscmvcEvent;

/**
 * The AbstractRouter class is the blueprint for the MVC's main router.
 *
 * The AbstractRouter class is the one that needs to be extended
 * in order to create a LightMVC router.
 */
abstract class AbstractRouter
{

    /**
     * Contains a reference to the Singleton instance of the AbstractApp class.
     *
     * @var AbstractApp|null
     */
    protected $app;

    /**
     * Array contains all of the app's basic configurations.
     *
     * @var array|null
     */
    protected $baseConfig;

    /**
     * Contains an associative array of all of the URI's elements.
     *
     * @var array|null
     */
    protected $requestURI;

    /**
     * Contains an instance of the MVC's AbstractControllerManager.
     *
     * @var AbstractControllerManager
     */
    protected $controllerManager;

    /**
     * Initializes this class by assigning the objects contained in the
     * referenced application object to the corresponding properties.
     *
     * @param AscmvcEvent $event
     *
     * @return void
     */
    public abstract function __construct(AscmvcEvent $event);

    /**
     * Get the current request URI
     *
     * @return array|null
     */
    public abstract function getRequestURI();
}
