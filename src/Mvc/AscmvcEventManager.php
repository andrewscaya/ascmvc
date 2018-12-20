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

use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManagerInterface;


class AscmvcEventManager extends EventManager {
	
    public function __construct(SharedEventManagerInterface $sharedEventManager = null, array $identifiers = [])
    {
        parent::__construct($sharedEventManager, $identifiers);

        $eventManager = $this;

        $this->attach(AscmvcEvent::EVENT_BOOTSTRAP, function ($event) use ($eventManager) {
            return $eventManager->onBootstrap($event);
        });

        $this->attach(AscmvcEvent::EVENT_DISPATCH, function ($event) use ($eventManager) {
            return $eventManager->onDispatch($event);
        }, 2);

        $this->attach(AscmvcEvent::EVENT_RENDER, function ($event) use ($eventManager) {
            return $eventManager->onRender($event);
        }, 2);

        $this->attach(AscmvcEvent::EVENT_RENDER, function($event) {
            return $event->getApplication()->render($event->getApplication()->getResponse());
        });

        $this->attach(AscmvcEvent::EVENT_FINISH, function ($event) use ($eventManager) {
            return $eventManager->onFinish($event);
        }, 2);

        $this->attach(AscmvcEvent::EVENT_FINISH, function($event) {
            return $event->getApplication()->display($event->getApplication()->getResponse());
        });
    }
    
    public function onBootstrap(AscmvcEvent $event)
    {
        $baseConfig = $event->getApplication()->getBaseConfig();

        $path = $baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'controllers';

        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        foreach($objects as $name => $fileInfo){
            if ($fileInfo->isFile() && preg_match('/^[A-Za-z0-9_\-]+Controller(?=.php)/', $fileInfo->getFilename())) {
                $filePath = $fileInfo->getPathName();
                require_once $filePath;

                $filePathArray = explode('/', $filePath);
                $fileName = array_pop($filePathArray);
                array_pop($filePathArray);
                $domainName = array_pop($filePathArray);
                $controllerName = $domainName. '\\Controllers\\' . substr($fileName, 0, strlen($fileName) - 4);

                $response = $controllerName::onBootstrap($event);

                if ($response instanceof Response) {
                    return $response;
                }
            }
        }
    }
	
    public function onDispatch(AscmvcEvent $event)
    {
        $controller = $event->getApplication()->getController();
        return $controller->onDispatch($event);
    }
    
    public function onRender(AscmvcEvent $event)
    {
        $controller = $event->getApplication()->getController();
        return $controller->onRender($event);
    }

    public function onFinish(AscmvcEvent $event)
    {
        $controller = $event->getApplication()->getController();

        if(isset($controller)) {
            return $controller->onFinish($event);
        } else {
            return;
        }
    }
    
}
