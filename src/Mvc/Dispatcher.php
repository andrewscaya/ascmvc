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

use Ascmvc\AbstractApp;
use Ascmvc\AbstractDispatcher;


class Dispatcher extends AbstractDispatcher {

    /**
     * Initializes this class by assigning the objects contained in the
     * referenced App object's router object to the corresponding properties.
     *
     * @param AbstractApp &$app.
     *
     * @return void.
     */
    public function __construct(AbstractApp &$app)
    {
        $this->app = $app;

        $this->router = $this->app->getRouter();

        $controllerName = ($this->router->getControllerReflection() != NULL) ? $this->router->getControllerName() : NULL;
        
        $this->controller = ($controllerName != NULL) ? new $controllerName($this->app) : NULL;
        
        $this->method = ($this->router->getCurrentRequestURI() != NULL) ? $this->router->getControllerMethodName() : NULL;

        if ($this->controller == NULL || $this->method == NULL) {

            header("Location: ". URLBASEADDR ."index.php/c404");

            exit;

        }
    }

    public function dispatch()
    {
        $this->controller->{$this->method}();
    }

}