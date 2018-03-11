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
use Ascmvc\AbstractRouter;


class Router extends AbstractRouter {

    public function __construct(AbstractApp &$app)
    {
        $this->app = $app;
        
        $this->baseConfig = $this->app->getBaseConfig();
        
        $this->currentRequestURI = $this->app->getRequest()->getRequestURI();
        
        
        $this->controllerName = (isset($this->currentRequestURI['controller'])) ? $this->currentRequestURI['controller'] . 'Controller' : 'IndexController';
        
        $this->controllerName = 'Application\\Controllers\\' . $this->controllerName;
        
        $this->controllerMethodName = (isset($this->currentRequestURI['method'])) ? $this->currentRequestURI['method'] . 'Action' : 'indexAction';
        
        
        try {
        
            $this->controllerReflection = new \ReflectionClass($this->controllerName);
        
            $this->controllerFile = $this->controllerReflection->getFileName();
            
            $this->controllerReflection->hasMethod($this->controllerMethodName);
        
        }
        catch (\ReflectionException $e) {
        
            $this->controllerReflection = NULL;
            
            $this->controllerName = NULL;
            
            $this->controllerMethodName = NULL;
        
            $this->currentRequestURI = NULL;
        
        }
    }

    public function getCurrentRequestURI()
    {
        return $this->currentRequestURI;
    }
    
    public function getControllerReflection()
    {
        return $this->controllerReflection;
    }
    
    public function getControllerName()
    {
        return $this->controllerName;
    }
    
    public function getControllerMethodName()
    {
        return $this->controllerMethodName;
    }
    
    public function getControllerFile()
    {
        return $this->controllerFile;
    }
    
}