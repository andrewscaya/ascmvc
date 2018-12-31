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
use Ascmvc\AbstractResponse;
use Ascmvc\AbstractRouter;
use Ascmvc\AbstractEventManager;
use Ascmvc\AbstractViewObject;
use Pimple\Container;
use Zend\Diactoros\ServerRequestFactory;


class App extends AbstractApp
{

    protected function __construct()
    {

    }

    protected function __clone()
    {

    }

    public static function getInstance()
    {
        if (!self::$appInstance) {

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
        $this->baseConfig = $baseConfig;

        $this->eventManager = AscmvcEventManagerFactory::create();
        $this->event = new AscmvcEvent(AscmvcEvent::EVENT_BOOTSTRAP);
        $this->event->setApplication($this);

        if (!isset($serviceManager)) {
            $this->serviceManager = new Container();
        } else {
            $this->serviceManager = $serviceManager;
        }

        if (!isset($this->request)) {
            $this->request = ServerRequestFactory::fromGlobals();
        }

        $this->router = new FastRouter($this->event);

        if (!isset($viewObject)) {

            $this->viewObject = ViewObject::getInstance($this->baseConfig);

        } else {

            $this->viewObject = $viewObject;

        }

        if (isset($this->baseConfig['doctrine'])) {

            foreach ($this->baseConfig['doctrine'] as $connType => $connections) {

                foreach ($connections as $connName => $params) {

                    $serviceManager = $this->serviceManager;

                    $serviceManager["$connName"] = $serviceManager->factory(function ($serviceManager) use ($connType, $connName, $params) {
                        $dbManager = Doctrine::getInstance($connType, $connName, $params);
                        return $dbManager;
                    });

                }

            }

        }

        return $this;
    }

    public function display(Response $response)
    {
        $header = $response->getHeader();
        header("HTTP/1.1 $header");
        echo $response;
        return;
    }

    public function render($controllerOutput)
    {
        if(is_array($controllerOutput)) {
            $viewObject = $this->getViewObject();

            if ($viewObject instanceof \Twig_Environment) {
                $twig = $viewObject->load($controllerOutput['templatefile']);
                echo $twig->render(['view' => $controllerOutput]);
            } elseif ($viewObject instanceof \Smarty) {
                $viewObject->assign('view', $controllerOutput);
                $viewObject->display($controllerOutput['templatefile']);
            }

            $response = new Response('200 OK', ob_get_clean());
        } else {
            $response = new Response('200 OK', $controllerOutput);
        }

        return $response;
    }
    
    public function run()
    {
        $event = $this->event;

        $shortCircuit = function ($response) use ($event) {
            if ($response instanceof Response) {
                return true;
            } else {
                return false;
            }
        };

        $this->event->stopPropagation(false); // Clear before triggering
        $result = $this->eventManager->triggerEventUntil($shortCircuit, $this->event);

        if ($result->stopped()) {
            $response = $result->last();
            if ($response instanceof Response) {
                $this->response = $response;
                $this->event->setName(AscmvcEvent::EVENT_FINISH);
                $this->event->stopPropagation(false); // Clear before triggering
                $this->eventManager->triggerEvent($this->event);
                return;
            }
        }
        
        $this->event->setName(AscmvcEvent::EVENT_ROUTE);
        $this->event->stopPropagation(false); // Clear before triggering
        $result = $this->eventManager->triggerEvent($this->event);
        
        $this->event->setName(AscmvcEvent::EVENT_DISPATCH);
        $this->event->stopPropagation(false); // Clear before triggering
        $result = $this->eventManager->triggerEventUntil($shortCircuit, $this->event);

        $response = $result->last();

        $this->response = $response;

        if ($result->stopped()) {
            if ($response instanceof Response) {
                $this->event->setName(AscmvcEvent::EVENT_FINISH);
                $this->event->stopPropagation(false); // Clear before triggering
                $this->eventManager->triggerEvent($this->event);
                return;
            }
        }

        $this->event->setName(AscmvcEvent::EVENT_RENDER);
        $this->event->stopPropagation(false); // Clear before triggering
        $result = $this->eventManager->triggerEventUntil($shortCircuit, $this->event);

        $response = $result->last();

        $this->response = $response;

        $this->event->setName(AscmvcEvent::EVENT_FINISH);
        $this->event->stopPropagation(false); // Clear before triggering
        $result = $this->eventManager->triggerEvent($this->event);
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

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(AbstractResponse $response)
    {
        $this->response = $response;
        return $this->response;
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

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent(AscmvcEvent &$event)
    {
        $this->event = $event;
        return $this->event;
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

    public function getViewObject()
    {
        return $this->viewObject;
    }

    public function setViewObject(AbstractViewObject &$viewObject)
    {
        $this->viewObject = $viewObject;

        return $this;
    }

}
