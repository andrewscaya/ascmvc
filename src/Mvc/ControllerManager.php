<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.3
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      1.0.0
 */

namespace Ascmvc\Mvc;

use Ascmvc\AbstractApp;
use Ascmvc\AbstractControllerManager;
use Ascmvc\AscmvcControllerFactoryInterface;
use Ascmvc\EventSourcing\AggregateImmutableValueObject;
use Ascmvc\EventSourcing\AggregateRootController;
use Ascmvc\EventSourcing\Event\AggregateEvent;
use Zend\Diactoros\Response;

/**
 * Class ControllerManager
 *
 * The ControllerManager class extends the AbstractControllerManager and
 * acts as the MVC's dispatcher object.
 *
 */
class ControllerManager extends AbstractControllerManager
{

    /**
     * Initializes this class by assigning the objects contained in the
     * referenced App object's router object to the corresponding properties.
     *
     * @param AbstractApp &$app
     * @param string $controllerName
     * @param array $vars
     *
     * @return void
     */
    public function __construct(AbstractApp &$app, $controllerName, array $vars = [])
    {
        $this->app = $app;

        $this->vars = $vars;

        $baseConfig = $this->app->getBaseConfigForControllers();

        $serviceManager = $this->app->getServiceManager();

        $eventManager = $this->app->getEventManager();

        $viewObject = $this->app->getViewObject();

        if (strpos($controllerName, '/') !== false) {
            $controllerNameArray = explode('/', trim($controllerName));
            $controllerName = ucfirst($controllerNameArray[1]) . 'Controller';
            $this->controllerName = ucfirst($controllerNameArray[0]) . '\\Controllers\\' . $controllerName;
        } else {
            $controllerName = ucfirst($controllerName) . 'Controller';
            $this->controllerName = 'Application\\Controllers\\' . $controllerName;
        }

        $this->controllerMethodName = (isset($this->vars['get']['action'])) ? $this->vars['get']['action'] . 'Action' : 'indexAction';

        $controllerName = $this->controllerName;

        $sharedEventManager = $eventManager->getSharedManager();

        $eventDispatcherName = $baseConfig['events']['psr14_event_dispatcher'];

        $eventDispatcher = new $eventDispatcherName($this->app, $sharedEventManager);

        try {
            $this->controllerReflection = new \ReflectionClass($controllerName);

            $this->controllerFileName = $this->controllerReflection->getFileName();

            if (!$this->controllerReflection->hasMethod($this->controllerMethodName)) {
                throw new \ReflectionException;
            }

            if ($this->controllerReflection->implementsInterface(AscmvcControllerFactoryInterface::class)
                && $this->controllerReflection->hasMethod('factory')
            ) {
                $controller = $controllerName::factory($baseConfig, $eventDispatcher, $serviceManager, $viewObject);
                $this->controller = $controller instanceof Controller ? $controller : null;
                $this->controller = !isset($this->controller) && isset($serviceManager[$controllerName]) ? $serviceManager[$controllerName] : null;
            }
        } catch (\ReflectionException $e) {
            $this->controllerReflection = null;

            $this->controllerName = null;

            $this->controllerMethodName = null;

            $this->currentRequestURI = null;

            $this->controller = null;
        }

        $this->controller = ($this->controller == null && $this->controllerName != null) ? new $controllerName($baseConfig, $eventDispatcher) : $this->controller;

        $this->method = ($this->controllerMethodName != null) ? $this->controllerMethodName : null;

        if ($this->controller == null || $this->method == null) {
            throw new \RuntimeException('Controller method not found');

            return;
        }

        $this->app->setController($this->controller);

        $event = new AggregateEvent(new AggregateImmutableValueObject(), get_class($this->controller), $this->method);

        $eventDispatcher->dispatch($event);

        return;
    }

    /**
     * Executes the request handler.
     *
     * @return Response|array|string
     */
    public function execute()
    {
        $preActionName = 'pre' . ucfirst($this->method);

        if ($this->controllerReflection->getParentClass()->getName() === AggregateRootController::class
            && $this->controllerReflection->hasMethod($preActionName)
        ) {
            $controllerPreActionOutput = $this->controller->{$preActionName}($this->vars);
        }

        $controllerActionOutput = $this->controller->{$this->method}($this->vars);

        if (isset($controllerPreActionOutput)
            && is_array($controllerPreActionOutput)
            && !empty($controllerPreActionOutput)
        ) {
            $controllerOutput = array_merge($controllerPreActionOutput, $controllerActionOutput);
        } else {
            $controllerOutput = $controllerActionOutput;
        }

        return $controllerOutput;
    }
}
