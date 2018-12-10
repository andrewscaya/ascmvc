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


class FastRouter extends AbstractRouter {

    public function __construct(AbstractApp &$app)
    {
		$this->app = $app;
		
		$this->baseConfig = $this->app->getBaseConfig();
		
        $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
			foreach ($this->baseConfig['routes'] as $singleRoute) {
				$r->addRoute($singleRoute[0], $singleRoute[1], $singleRoute[2]);
			}
		} /*[
			'cacheFile' => $this->baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'routes.cache', // required
			'cacheDisabled' => false,     // optional, enabled by default
		]*/);
		
		$this->requestURI = $this->app->getRequest()->getRequestURI();
		
		// Fetch method and URI from somewhere
		$httpMethod = $this->requestURI['httpmethod'];
		$uri = $this->requestURI['uri'];
		
		$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
		
		switch ($routeInfo[0]) {
			case FastRoute\Dispatcher::NOT_FOUND:
				// ... 404 Not Found
				$this->controllerManager = new ControllerManager($this->app, 'c404');
				$this->app->setControllerManager($this->controllerManager);
				break;
			case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
				$allowedMethods = $routeInfo[1];
				// ... 405 Method Not Allowed
				$this->controllerManager = new ControllerManager($this->app, 'c405', $allowedMethods);
				$this->app->setControllerManager($this->controllerManager);
				break;
			case FastRoute\Dispatcher::FOUND:
				$controller = $routeInfo[1];
				$vars = $routeInfo[2];
				// ... call $handler with $vars
				$this->controllerManager = new ControllerManager($this->app, $controller, $vars);
				$this->app->setControllerManager($this->controllerManager);
				break;
		}
    }

    public function getRequestURI()
    {
        return $this->requestURI;
    }
    
}
