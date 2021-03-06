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

use Ascmvc\EventSourcing\EventDispatcher;

/**
 * The abstract AbstractController class is the blueprint for the MVC's controllers.
 *
 * *Description* The AbstractController class is the one that needs to be extended
 * in order to create a LightMVC controller.
 */
abstract class AbstractController
{

    /**
     * Contains a reference to the array containing some of the app's basic configurations
     * useful to controllers.
     *
     * @var array|null
     */
    protected $baseConfig;

    /**
     * Array contains all of the values that will be assigned to the View manager's template.
     *
     * @var array|null
     */
    protected $view;

    /**
     * Contains an instance of the Ascmvc\EventSourcing\EventDispatcher class.
     *
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * Array contains the controller's helper objects.
     *
     * @var array|null
     */
    protected $helpers;

    /**
     * Initializes this class by assigning the objects and arrays
     * contained in the referenced application object to the corresponding
     * properties.
     *
     * @param array $baseConfig
     * @param EventDispatcher $eventDispatcher
     */
    public abstract function __construct(array $baseConfig, EventDispatcher $eventDispatcher);

    /**
     * Method corresponding to the controller's default action.
     *
     * @param null $vars
     *
     * @return mixed|void
     */
    public abstract function indexAction($vars = null);
}
