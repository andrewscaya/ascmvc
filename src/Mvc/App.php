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
use Ascmvc\AbstractDispatcher;
use Ascmvc\AbstractRouter;
use Ascmvc\AbstractServiceManager;
use Ascmvc\AbstractViewObject;


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
    
    public function initialize(Array &$baseConfig, AbstractServiceManager &$serviceManager = NULL, \Smarty &$viewObject = NULL)
    {
        if (!isset($this->request)) {
        
            $this->request = new Request($this);
        
        }
        
        
        if (!isset($serviceManager)) {
            
            $this->serviceManager = new ServiceManager();
            
        }
        else {
            
            $this->serviceManager = $serviceManager;
            
        }
        
        
        $this->baseConfig = $baseConfig;
        
        
        if (isset($this->baseConfig['doctrine'])) {
            
            foreach ($this->baseConfig['doctrine'] as $connType => $connections) {
                
                foreach ($connections as $connName => $params) {
                    
                    $dbManager = Doctrine::getInstance($connType, $connName, $params);
                    
                    $this->serviceManager->addRegisteredService($connName, $dbManager);
                    
                }
                
            }
            
        }
        
        
        if (!isset($viewObject)) {
        
            $this->viewObject = Smarty::getInstance();
        
        }
        else {
        
            $this->viewObject = $viewObject;
        
        }
        
        $this->viewObject->setTemplateDir($this->baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR);
        $this->viewObject->setCompileDir($this->baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'templates_c' . DIRECTORY_SEPARATOR);
        $this->viewObject->setConfigDir($this->baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
        $this->viewObject->setCacheDir($this->baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);
        $this->viewObject->caching = 0;
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

    public function setServiceManager(AbstractServiceManager &$serviceManager)
    {
        $this->serviceManager = $serviceManager;

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
    
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
    
    public function setDispatcher(AbstractDispatcher &$dispatcher)
    {
        $this->dispatcher = $dispatcher;
        
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
        $this->getServiceManager()->updateRunLevel($this, $currentRunLevel);
        
        $this->currentRunLevel = $currentRunLevel;
        
        return $this;
    }
    
    public function run()
    {
        $this->setCurrentRunLevel('preboot');
        
        $this->router = new Router($this);
        
        $this->dispatcher = new Dispatcher($this);
        
        $this->setCurrentRunLevel('postboot');
        
        $this->serviceManager->addRegisteredListener('controller', $this->controller);
                
        $this->setCurrentRunLevel('predispatch');
        
        $this->dispatcher->dispatch();
        
        $this->setCurrentRunLevel('postdispatch');
        
        $this->setCurrentRunLevel('preresponse');
    }

}