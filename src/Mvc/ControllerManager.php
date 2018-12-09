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
use Ascmvc\AbstractControllerManager;


class ControllerManager extends AbstractControllerManager {

    /**
     * Initializes this class by assigning the objects contained in the
     * referenced App object's router object to the corresponding properties.
     *
     * @param AbstractApp &$app.
     *
     * @return void.
     */
    public function __construct(AbstractApp &$app, $controller, array $vars = [])
    {
		$this->app = $app;
		
        $this->controllerName = ucfirst($controller) . 'Controller';
        
        $this->controllerName = 'Application\\Controllers\\' . $this->controllerName;
        
        $this->controllerMethodName = (isset($vars['action'])) ? $vars['action'] . 'Action' : 'indexAction';
        
        try {
        
            $this->controllerReflection = new \ReflectionClass($this->controllerName);
        
            $this->controllerFileName = $this->controllerReflection->getFileName();
            
            $this->controllerReflection->hasMethod($this->controllerMethodName);
        
        }
        catch (\ReflectionException $e) {
        
            $this->controllerReflection = null;
            
            $this->controllerName = null;
            
            $this->controllerMethodName = null;
        
            $this->currentRequestURI = null;
        
        }
        
        $controllerName = $this->controllerName;
        
        $sm = $this->app->getServiceManager();
        
        if($this->controllerReflection->hasMethod('factory')) {
			$controllerName::factory($this->app);
			$this->controller = isset($sm[$controllerName]) ? $sm[$controllerName] : null;
		}
        
        $this->controller = ($this->controller == null && $controllerName != null) ? new $controllerName($this->app) : $this->controller;
        
        $this->method = ($this->controllerMethodName != null) ? $this->controllerMethodName : null;

        if ($this->controller == null || $this->method == null) {

            header("Location: ". URLBASEADDR ."c404");

            exit;

        }
    }

    public function execute()
    {
        $controllerOutput = $this->controller->{$this->method}();
    }

}
