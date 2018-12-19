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


class AscmvcEventManager extends EventManager {
	
	public function __construct(SharedEventManagerInterface $sharedEventManager = null, array $identifiers = [])
	{
		parent::__construct($sharedEventManager, $identifiers);
		
		$this->attach(AscMvcEvent::EVENT_BOOTSTRAP, array($this, 'onBootstrap');
		
		$this->attach(AscMvcEvent::EVENT_DISPATCH, array($this, 'onDispatch');
		
		$this->attach(AscMvcEvent::EVENT_RENDER, array($this, 'onRender');
	}
    
    public function onBootstrap(AscmvcEvent $event)
    {
		$baseConfig = $event->getApplication()->getBaseConfig();
            
		$path = $baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Controllers';
		
		$dirIterator = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
		
		foreach ($dirIterator as $fileInfo) {
			if ($fileInfo->isFile() && preg_match('/^[A-Za-z0-9_\-]+Controller(?=.php)/', $fileInfo->getFilename())) {
				
				$fileName = $fileInfo->getFilename();
				
				$controllerName = 'Application\\Controllers\\' . substr($fileName, 0, strlen($fileName) - 4);
		
				require_once $path . DIRECTORY_SEPARATOR . $fileName;
				
				$controllerName::onBootstrap($event);
			}
		}
	}
	
    public function onDispatch(AscmvcEvent $event)
    {
		$controller = $event->getApplication()->getController();
		$controller->onDispatch($event);
	}
    
    public function onRender(AscmvcEvent $event)
    {
		$controller = $event->getApplication()->getController();
		$controller->onResponse($event);
	}
    
}
