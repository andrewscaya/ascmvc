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
 * The abstract AbstractDispatcher class is the blueprint for the MVC's dispatchers.
 *
 * The AbstractDispatcher class is the one that needs to be extended
 * in order to create a LightMVC dispatcher.
 */
abstract class AbstractDispatcher {

    /**
     * Contains a reference to the Singleton instance of the App class.
     *
     * @var AbstractApp|null
     */
    protected $app;
    
    /**
     * Contains a reference to an instance of the Router class.
     *
     * @var AbstractRouter|null
     */
    protected $router;

    /**
     * Contains a reference to a polymorphic Controller instance.
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
     * Initializes this class by assigning the objects contained in the
     * referenced App object's router object to the corresponding properties.
     *
     * @param AbstractApp &$app.
     *
     * @return void.
     */
    public abstract function __construct(AbstractApp &$app);

    /**
     * Method that calls the MVC's appropriate request handler.
     *
     * @param void.
     *
     * @return void.
     */
    public abstract function dispatch();

}