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
use Ascmvc\AbstractController;
use Ascmvc\AbstractControllerManager;
use Ascmvc\AbstractRouter;
use Ascmvc\AbstractEventManager;
use Ascmvc\AbstractViewObject;
use Pimple\Container;


class App extends AbstractApp {
    
    protected function __construct()
    {
    
    }
    
    protected function __clone()
    {
        
    }

    public static function getInstance()
    {
        if(!self::$appInstance) {
    
            self::$appInstance = new App();
    
        }
    
        return self::$appInstance;
    }
    
    public function initialize(Array &$baseConfig, Container &$serviceManager = null, ViewObject &$viewObject = null)
    {
        if (!isset($this->request)) {
        
            $this->request = new Request($this);
        
        }
        
        
        if (!isset($serviceManager)) {
            
            $this->serviceManager = new Container();
            
        }
        else {
            
            $this->serviceManager = $serviceManager;
            
        }
        
        $this->eventManager = new EventManager();
        
        
        $this->baseConfig = $baseConfig;
        
        
        if (isset($this->baseConfig['doctrine'])) {
            
            foreach ($this->baseConfig['doctrine'] as $connType => $connections) {
                
                foreach ($connections as $connName => $params) {
                    
                    $dbManager = Doctrine::getInstance($connType, $connName, $params);
                    
                    $this->serviceManager["$connName"] = $dbManager;
                    
                }
                
            }
            
        }
        
        if (!isset($viewObject)) {
			
            $this->viewObject = ViewObject::getInstance($this->baseConfig);
        
        }
        else {
        
            $this->viewObject = $viewObject;
        
        }
    }
    
    public function getBaseConfig()
    {
        return $this->baseConfig;
    }
    
    public function appendBaseConfig($name, $array)
    {
        $this->baseConfig[$name] = $array;
    
        return $this->baseConfig;
    }
    
    public function getRequest()
    {
        return $this->request;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function setServiceManager(Container &$serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
    
    public function getEventManager()
    {
        return $this->eventManager;
    }

    public function setEventManager(AbstractEventManager &$eventManager)
    {
        $this->eventManager = $eventManager;

        return $this;
    }

    public function getViewObject()
    {
        return $this->viewObject;
    }

    public function setViewObject(AbstractViewObject &$viewObject)
    {
        $this->viewObject = $viewObject;

        return $this;
    }
    
    public function getRouter()
    {
        return $this->router;
    }
    
    public function setRouter(AbstractRouter &$router)
    {
        $this->router = $router;
        
        return $this;
    }
    
    public function getControllerManager()
    {
        return $this->controllerManager;
    }
    
    public function setControllerManager(AbstractControllerManager &$controllerManager)
    {
        $this->controllerManager = $controllerManager;
        
        return $this;
    }
    
    public function getController()
    {
        return $this->controller;
    }
    
    public function setController(AbstractController &$controller)
    {
        $this->controller = $controller;
        
        return $this;
    }
    
    public function getCurrentRunLevel()
    {
        return $this->currentRunLevel;
    }

    public function setCurrentRunLevel($currentRunLevel)
    {
        $this->getEventManager()->updateRunLevel($this, $currentRunLevel);
        
        $this->currentRunLevel = $currentRunLevel;
        
        return $this;
    }
    
    public function run()
    {
        $this->setCurrentRunLevel('preboot');
        
        $this->router = new FastRouter($this);
        
        $this->setCurrentRunLevel('postboot');
        
        $this->eventManager->addRegisteredListener('controller', $this->controller);
                
        $this->setCurrentRunLevel('predispatch');
        
        $this->controllerManager->execute();
        
        $this->setCurrentRunLevel('postdispatch');
        
        $this->setCurrentRunLevel('preresponse');
    }

}
