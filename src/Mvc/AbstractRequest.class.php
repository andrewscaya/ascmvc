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
 * The AbstractRequest class is the blueprint for the MVC's main Request object.
 *
 * *Description* The AbstractRequest class is the one that needs to be extended
 * in order to create a LightMVC Request object.
 */
abstract class AbstractRequest {
    
    /**@var Object:App|null  Contains a reference to the Singleton instance of the App class.*/
    protected $app;
    
    /**@var array|null  Array contains all of the app's basic configurations.*/
    protected $baseConfig;
    
    /**@var array|null  Contains an associative array of all of the URI's elements.*/
    protected $requestURI;
    
    
    /**
     * Initializes this class by assigning the elements of the URI to the
     * array $requestURI.
     *
     * @param Object:App &$app.
     *
     * @return void.
     */
    public abstract function __construct(App &$app);
    
    /**
     * @return array|null $requestURI
     */
    public abstract function getRequestURI();
        
}