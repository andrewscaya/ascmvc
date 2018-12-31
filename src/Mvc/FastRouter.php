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
use FastRoute;
use Zend\Diactoros\Response;


class FastRouter extends AbstractRouter {

    public function __construct(AscmvcEvent $event)
    {
        $this->app = $event->getApplication();

        $this->baseConfig = $this->app->getBaseConfig();
    }

    public function resolve()
    {
        if($this->baseConfig['env'] === 'production') {
            $dispatcher = FastRoute\cachedDispatcher(function(FastRoute\RouteCollector $r) {
                foreach ($this->baseConfig['routes'] as $singleRoute) {
                    $r->addRoute($singleRoute[0], $singleRoute[1], $singleRoute[2]);
                }
            }, [
                'cacheFile' => $this->baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'routes.cache', // required
                'cacheDisabled' => false,     // optional, enabled by default
            ]);
        } else {
            $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
                foreach ($this->baseConfig['routes'] as $singleRoute) {
                    $r->addRoute($singleRoute[0], $singleRoute[1], $singleRoute[2]);
                }
            });
        }

        // Fetch method and URI
        $this->requestURI = $this->app->getRequest()->getServerParams();
        $httpMethod = $this->app->getRequest()->getMethod();
        $uri = $this->requestURI['REQUEST_URI'];

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                $this->controllerManager = new ControllerManager($this->app, 'c404');
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                $this->controllerManager = new ControllerManager($this->app, 'c405', $allowedMethods);
                break;
            case FastRoute\Dispatcher::FOUND:
                $controller = $routeInfo[1];
                $vars = [
                    'get' => $routeInfo[2],
                    'post' => $this->app->getRequest()->getParsedBody(),
                    'files' => $this->app->getRequest()->getUploadedFiles(),
                    'cookies' => $this->app->getRequest()->getCookieParams(),
                    'server' => $this->requestURI,
                ];
                // ... call $handler with $vars
                $this->controllerManager = new ControllerManager($this->app, $controller, $vars);
                break;
        }

        $this->app->setControllerManager($this->controllerManager);

        $controllerManager = $this->controllerManager;

        $eventManager = $this->app->getEventManager();
        $eventManager->attach(AscmvcEvent::EVENT_DISPATCH, function ($event) use ($controllerManager) {
            return $controllerManager->execute();
        });

        return;
    }

    public function getRequestURI()
    {
        return $this->requestURI;
    }
    
}
