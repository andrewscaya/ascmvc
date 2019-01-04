<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.0
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      1.0.0
 */

namespace Ascmvc\Mvc;

use Ascmvc\AbstractApp;
use Ascmvc\AbstractController;
use Ascmvc\AbstractControllerManager;
use Ascmvc\AbstractRouter;
use Ascmvc\AbstractEventManager;
use Ascmvc\AbstractViewObject;
use Pimple\Container;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Stratigility\Exception\EmptyPipelineException;

use function Zend\Stratigility\middleware;
use function Zend\Stratigility\path;

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

        if (file_exists(BASEDIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.local.php')) {
            require_once BASEDIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.local.php';
        } else {
            require_once BASEDIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
        }

        return $baseConfig;
    }

    public function initialize(array &$baseConfig)
    {
        $this->baseConfig = $baseConfig;

        if (!isset($this->request)) {
            $this->request = ServerRequestFactory::fromGlobals();
        }

        $this->serviceManager = new Container();
        $serviceManager = $this->serviceManager;

        $this->eventManager = AscmvcEventManagerFactory::create();
        $this->event = new AscmvcEvent(AscmvcEvent::EVENT_BOOTSTRAP);
        $this->event->setApplication($this);

        $this->router = new FastRouter($this->event);

        $this->viewObject = ViewObjectFactory::getInstance($this->baseConfig);

        if (isset($this->baseConfig['doctrine'])) {
            foreach ($this->baseConfig['doctrine'] as $connType => $connections) {
                foreach ($connections as $connName => $params) {
                    $serviceManager["$connName"] = $serviceManager->factory(function ($serviceManager) use ($connType, $connName, $params) {
                        $dbManager = Doctrine::getInstance($connType, $connName, $params);
                        return $dbManager;
                    });
                }
            }
        }

        $middlewarePipe = new MiddlewarePipe();

        if (isset($this->baseConfig['middleware'])) {
            foreach ($this->baseConfig['middleware'] as $path => $handler) {
                if (strpos($path, '/') !== false) {
                    if (is_callable($handler) && $handler instanceof \Closure) {
                        $middlewarePipe->pipe(path($path, middleware($handler)));
                    } else {
                        $middlewarePipe->pipe(path($path, (new $handler($this->baseConfig))));
                    }
                } else {
                    if (is_callable($handler) && $handler instanceof \Closure) {
                        $middlewarePipe->pipe(middleware($handler));
                    } else {
                        $middlewarePipe->pipe((new $handler));
                    }
                }
            }

            $serviceManager['middleware'] = function ($serviceManager) use ($middlewarePipe) {
                return $middlewarePipe;
            };

            $this->eventManager->attach(AscmvcEvent::EVENT_BOOTSTRAP, function ($event) use ($serviceManager) {
                $middlewarePipe = $serviceManager['middleware'];
                try {
                    $response = $middlewarePipe->handle($this->request);
                } catch (EmptyPipelineException $e) {
                    return false;
                }

                return $response;
            }, 3);
        }

        return $this;
    }

    public function display(Response $response)
    {
        $statusCode = $response->getStatusCode();
        $protocolVersion = $this->request->getProtocolVersion();
        header("HTTP/$protocolVersion $statusCode");
        $headers = $response->getHeaders();

        foreach ($response->getHeaders() as $header => $value) {
            header("$header: $value[0]");
        }

        if (!empty($response->getBody())) {
            echo $response->getBody();
        }

        return;
    }

    public function render($controllerOutput)
    {
        $response = new Response();

        if (is_array($controllerOutput)) {
            $viewObject = $this->viewObject;

            if ($viewObject instanceof \League\Plates\Engine) {
                echo $viewObject->render($controllerOutput['templatefile'], ['view' => $controllerOutput]);
            } elseif ($viewObject instanceof \Twig_Environment) {
                $twig = $viewObject->load($controllerOutput['templatefile'] . '.html.twig');
                echo $twig->render(['view' => $controllerOutput]);
            } elseif ($viewObject instanceof \Smarty) {
                $viewObject->assign('view', $controllerOutput);
                $viewObject->display($controllerOutput['templatefile'] . '.tpl');
            }

            $response->getBody()->write(ob_get_clean());
        } else {
            $response->getBody()->write($controllerOutput);
        }

        if (isset($controllerOutput['statuscode'])) {
            $response = $response->withStatus($controllerOutput['statuscode']);
        } else {
            $response = $response->withStatus(200);
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
        $this->eventManager->triggerEvent($this->event);
    }

    public function getBaseConfig()
    {
        return $this->baseConfig;
    }

    public function getBaseConfigForControllers()
    {
        $baseConfig = $this->getBaseConfig();
        unset($baseConfig['doctrine']);
        unset($baseConfig['routes']);
        unset($baseConfig['templates']);

        return $baseConfig;
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

    public function setResponse(Response $response)
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

    public function setEventManager(AscmvcEventManager &$eventManager)
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

    public function setViewObject(&$viewObject)
    {
        $this->viewObject = $viewObject;

        return $this;
    }
}
