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
 * The AbstractResponse class is the blueprint for the MVC's main Response object.
 *
 * The AbstractResponse class is the one that needs to be extended
 * in order to create a LightMVC Response object.
 */
abstract class AbstractResponse {
    
    /**
     * Contains the body of the Response.
     *
     * @var string
     */
    protected $response;
    
    
    /**
     * Initializes this class by assigning some content to the $response property.
     *
     * @param strin $content.
     *
     * @return void.
     */
    public abstract function __construct(string $content);
    
    /**
     * @return string
     */
    public abstract function getResponse();
        
}