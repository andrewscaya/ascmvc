<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.1.2
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace Ascmvc\Mvc;

use Zend\Diactoros\Response;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManagerInterface;

use function Ascmvc\getNamespaceFromPath;

/**
 * Class AscmvcEventManager
 *
 * The AscmvcEventManager class extends the Zend\EventManager\EventManager class and
 * adds events and logic that are specific to this MVC.
 *
 */
class AscmvcEventManager extends EventManager
{
    /**
     * AscmvcEventManager constructor.
     *
     * @param SharedEventManagerInterface|null $sharedEventManager
     * @param array $identifiers
     */
    public function __construct(SharedEventManagerInterface $sharedEventManager = null, array $identifiers = [])
    {
        parent::__construct($sharedEventManager, $identifiers);

        $eventManager = $this;

        $this->attach(AscmvcEvent::EVENT_BOOTSTRAP, function ($event) use ($eventManager) {
            return $eventManager->onBootstrap($event);
        });

        $this->attach(AscmvcEvent::EVENT_ROUTE, function ($event) use ($eventManager) {
            return $eventManager->onRoute($event);
        });

        $this->attach(AscmvcEvent::EVENT_DISPATCH, function ($event) use ($eventManager) {
            return $eventManager->onDispatch($event);
        }, 2);

        $this->attach(AscmvcEvent::EVENT_RENDER, function ($event) use ($eventManager) {
            return $eventManager->onRender($event);
        }, 2);

        $this->attach(AscmvcEvent::EVENT_RENDER, function ($event) {
            return $event->getApplication()->render($event->getApplication()->getControllerOutput());
        });

        $this->attach(AscmvcEvent::EVENT_FINISH, function ($event) use ($eventManager) {
            return $eventManager->onFinish($event);
        }, 2);

        $this->attach(AscmvcEvent::EVENT_FINISH, function ($event) {
            return $event->getApplication()->display($event->getApplication()->getResponse());
        });
    }

    /**
     * Is triggered at runtime before the instantiation of the Router, Dispatcher and
     * Controller classes.
     *
     * @param AscmvcEvent $event
     *
     * @return Response|bool
     */
    public function onBootstrap(AscmvcEvent $event)
    {
        $baseConfig = $event->getApplication()->getBaseConfig();

        $path = $baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'controllers';

        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($objects as $name => $fileInfo) {
            if ($fileInfo->isFile() && preg_match('/^[A-Za-z0-9_\-]+Controller(?=.php)/', $fileInfo->getFilename())) {
                $filePath = $fileInfo->getPathName();

                require_once $filePath;

                $controllerName = getNamespaceFromPath($filePath);

                $response = $controllerName::onBootstrap($event);

                if ($response instanceof Response) {
                    return $response;
                }
            }
        }

        // @codeCoverageIgnoreStart
        return true;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Is triggered at runtime when the router tries to resolve the route.
     *
     * @param AscmvcEvent $event
     *
     * @return mixed
     */
    public function onRoute(AscmvcEvent $event)
    {
        $router = $event->getApplication()->getRouter();
        return $router->resolve();
    }

    /**
     * Is triggered at runtime after the registration of the controller object,
     * but before the controller manager's call to the controller's action method.
     *
     * @param AscmvcEvent $event
     *
     * @return mixed
     */
    public function onDispatch(AscmvcEvent $event)
    {
        $controller = $event->getApplication()->getController();
        return $controller->onDispatch($event);
    }

    /**
     * Is triggered at runtime when the Template Manager parses the view templates and
     * the controller's output.
     *
     * @param AscmvcEvent $event
     *
     * @return mixed
     */
    public function onRender(AscmvcEvent $event)
    {
        $controller = $event->getApplication()->getController();
        return $controller->onRender($event);
    }

    /**
     * Is triggered at runtime before flushing the buffers.
     *
     * @param AscmvcEvent $event
     *
     * @return null
     */
    public function onFinish(AscmvcEvent $event)
    {
        $controller = $event->getApplication()->getController();

        if (isset($controller)) {
            $controller->onFinish($event);
        }

        return;
    }
}
