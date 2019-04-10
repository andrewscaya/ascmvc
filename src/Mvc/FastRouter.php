<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.3
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace Ascmvc\Mvc;

use Ascmvc\AbstractRouter;
use FastRoute;

/**
 * Class FastRouter
 *
 * The FastRouter class extends the AbstractRouter class and uses the nikic/fast-route library.
 */
class FastRouter extends AbstractRouter
{
    /**
     * FastRouter constructor.
     * @param AscmvcEvent $event
     */
    public function __construct(AscmvcEvent $event)
    {
        $this->app = $event->getApplication();

        $this->baseConfig = $this->app->getBaseConfig();
    }

    /**
     * This method tries to find a handler that corresponds to the requested route.
     */
    public function resolve()
    {
        if ($this->baseConfig['env'] === 'production') {
            $dispatcher = FastRoute\cachedDispatcher(function (FastRoute\RouteCollector $r) {
                foreach ($this->baseConfig['routes'] as $singleRoute) {
                    $r->addRoute($singleRoute[0], $singleRoute[1], $singleRoute[2]);
                }
            }, [
                'cacheFile' => $this->baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'routes.cache', // required
                'cacheDisabled' => false,     // optional, enabled by default
            ]);
        } else {
            $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
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
                try {
                    $this->controllerManager = new ControllerManager($this->app, $controller, $vars);
                } catch (\Exception $e) {
                    // ... 404 Not Found
                    $this->controllerManager = new ControllerManager($this->app, 'c404');
                }

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

    /**
     * Returns an array containing the request's parameters.
     *
     * @return array
     */
    public function getRequestURI() : array
    {
        return $this->requestURI;
    }
}
