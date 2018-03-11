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
 * The AbstractRequest class is the blueprint for the MVC's main Request object.
 *
 * The AbstractRequest class is the one that needs to be extended
 * in order to create a LightMVC Request object.
 */
abstract class AbstractRequest {
    
    /**
     * Contains a reference to the Singleton instance of the App class.
     *
     * @var AbstractApp|null
     */
    protected $app;
    
    /**
     * Array contains all of the app's basic configurations.
     *
     * @var Array|null
     */
    protected $baseConfig;
    
    /**
     * Contains an associative array of all of the URI's elements.
     *
     * @var array|null
     */
    protected $requestURI;
    
    
    /**
     * Initializes this class by assigning the elements of the URI to the
     * array $requestURI.
     *
     * @param AbstractApp &$app.
     *
     * @return void.
     */
    public abstract function __construct(AbstractApp &$app);
    
    /**
     * @return array|null
     */
    public abstract function getRequestURI();
        
}