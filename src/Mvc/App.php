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

    public function boot()
    {
        if (PHP_SAPI !== 'cli') {
            $_SERVER['SERVER_SIGNATURE'] = isset($_SERVER['SERVER_SIGNATURE']) ? $_SERVER['SERVER_SIGNATURE'] : '80';

            $protocol = strpos($_SERVER['SERVER_SIGNATURE'], '443') !== false ? 'https://' : 'http://';

            $requestUriArray = explode('/', $_SERVER['PHP_SELF']);

            if (is_array($requestUriArray)) {
                $indexKey = array_search('index.php', $requestUriArray);

                array_splice($requestUriArray, $indexKey);

                $requestUri = implode('/', $requestUriArray);
            }

            $requestUrl = $protocol . $_SERVER['HTTP_HOST'] . $requestUri . '/';

            define('URLBASEADDR', $requestUrl);
        } else {
            define('URLBASEADDR', false);
        }


        $appFolder = basename(BASEDIR);

        $baseConfig = ['BASEDIR' => BASEDIR,
            'URLBASEADDR' => URLBASEADDR,
            'appFolder' => $appFolder,
        ];

        require_once BASEDIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

        if (file_exists(BASEDIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.local.php')) {
            include_once BASEDIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.local.php';
        }

        return $baseConfig;
    }
    
    public function initialize(array &$baseConfig, Container &$serviceManager = null, ViewObject &$viewObject = null)
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
        
        $eventManagerFactory = new AscmvcEventManagerFactory();
        $this->eventManager = $eventManagerFactory->factory();
        $this->event = new AscmvcEvent();

        return $this;
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
		$this->event->setName(MvcEvent::EVENT_BOOTSTRAP);
        $this->event->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEvent($this->event);
        
        //$this->router = new FastRouter($this);
        
        $this->event->setName(MvcEvent::EVENT_ROUTE);
        $this->event->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEvent($this->event);
        
        //$this->eventManager->addRegisteredListener('controller', $this->controller);
        
        $this->event->setName(MvcEvent::EVENT_DISPATCH);
        $this->event->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEvent($this->event);
        
        //$controllerOutput = $this->controllerManager->execute();
        
        $this->event->setName(MvcEvent::EVENT_RENDER);
        $this->event->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEvent($this->event);
        
        if (is_object($controllerOutput) && $controllerOutput instanceof Response) {
            echo $controllerOutput;
        } elseif(is_array($controllerOutput)) {
            $viewObject = $this->getViewObject();

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
        
        $this->event->setName(MvcEvent::EVENT_FINISH);
        $this->event->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEvent($this->event);
    }

}
