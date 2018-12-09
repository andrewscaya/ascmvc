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
 * The AbstractRouter class is the blueprint for the MVC's main router.
 *
 * The AbstractRouter class is the one that needs to be extended
 * in order to create a LightMVC router.
 */
abstract class AbstractRouter {
    
    /**
     * Contains a reference to the Singleton instance of the App class.
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
     * Contains an instance of the MVC's ControllerManager.
     *
     * @var AbstractControllerManager
     */
    protected $controllerManager;
    
    /**
     * Initializes this class by assigning the objects contained in the
     * referenced App object to the corresponding properties.
     *
     * @param AbstractApp &$app.
     *
     * @return void.
     */
    public abstract function __construct(AbstractApp &$app);
    
    /**
     * Get the current request URI.
     *
     * @return array|null
     */
    public abstract function getRequestURI();
    
}
