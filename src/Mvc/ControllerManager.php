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
     * @param string $controllerName.
     * @param array $vars.
     *
     * @return void.
     */
    public function __construct(AbstractApp &$app, $controllerName, array $vars = [])
    {
		$this->app = $app;
		
		$this->vars = $vars;
		
		$baseConfig = $this->app->getBaseConfig();
		
		unset($baseConfig['doctrine']);
		unset($baseConfig['routes']);
		unset($baseConfig['templates']);

        $serviceManager = $this->app->getServiceManager();

        $evenManager = $this->app->getEventManager();

        $viewObject = $this->app->getViewObject();
		
        $this->controllerName = ucfirst($controllerName) . 'Controller';
        
        $this->controllerName = 'Application\\Controllers\\' . $this->controllerName;
        
        $this->controllerMethodName = (isset($this->vars['action'])) ? $this->vars['action'] . 'Action' : 'indexAction';
        
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
        
        if($this->controllerReflection->hasMethod('factory')) {
			$controllerName::factory($baseConfig, $viewObject, $serviceManager, $evenManager);
			$this->controller = isset($serviceManager[$controllerName]) ? $serviceManager[$controllerName] : null;
		}
        
        $this->controller = ($this->controller == null && $controllerName != null) ? new $controllerName($baseConfig) : $this->controller;
        
        $this->method = ($this->controllerMethodName != null) ? $this->controllerMethodName : null;

        if ($this->controller == null || $this->method == null) {

            header("Location: ". URLBASEADDR ."c404");

            exit;

        } else {
			$this->app->setController($this->controller);
		}
    }

    public function execute()
    {
        if (!empty($this->vars)) {
            $controllerOutput = $this->controller->{$this->method}($this->vars);
        } else {
            $controllerOutput = $this->controller->{$this->method}();
        }

        if (is_object($controllerOutput) && $controllerOutput instanceof Response) {
            echo $controllerOutput;
        } elseif(is_array($controllerOutput)) {
            $viewObject = $this->app->getViewObject();

            if ($viewObject instanceof \Twig_Environment) {
                $twig = $viewObject->load($controllerOutput['templatefile']);
                echo $twig->render(['view' => $controllerOutput]);
            } elseif ($viewObject instanceof \Smarty) {
                $viewObject->assign('view', $controllerOutput);
                $viewObject->display($controllerOutput['templatefile']);
            }
        } else {
		    $response = new Response($controllerOutput);
		    echo $response;
        }
    }

}
