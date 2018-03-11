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
use Ascmvc\AbstractServiceManager;
use Ascmvc\ServiceManagerListenerInterface;
use Ascmvc\ServiceManagerServiceInterface;


class ServiceManager extends AbstractServiceManager {

    public function addRegisteredService($serviceName, &$service)
    {
        $this->registeredServices[$serviceName] = $service;
        
        return $this;
    }
    
    public function removeRegisteredService($serviceName)
    {
        unset($this->registeredServices[$serviceName]);
    
        return $this;
    }
    
    public function getRegisteredService($serviceName)
    {        
        return $this->registeredServices[$serviceName];
    }
    
    public function addRegisteredListener($listenerName, ServiceManagerListenerInterface &$listener)
    {
        $this->registeredListeners[$listenerName] = $listener;
        
        return $this;
    }
    
    public function removeRegisteredListener($listenerName)
    {
        unset($this->registeredListeners[$listenerName]);
    
        return $this;
    }
    
    public function getRegisteredListener($listenerName)
    {
        return $this->registeredListeners[$listenerName];
    }
    
    public function processEvents(AbstractApp &$app)
    {
        if ($this->runLevel == 'preboot' || $this->runLevel == 'postboot') {
            
            $baseConfig = $app->getBaseConfig();
            
            $path = $baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Controllers';
            
            $dirIterator = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
            
            foreach ($dirIterator as $fileInfo) {
            
                if ($fileInfo->isFile() && preg_match('/^[A-Za-z0-9_\-]+Controller(?=.php)/', $fileInfo->getFilename())) {
                    
                    $fileName = $fileInfo->getFilename();
                    
                    $controllerName = 'Application\\Controllers\\' . substr($fileName, 0, strlen($fileName) - 4);
            
                    require_once $path . DIRECTORY_SEPARATOR . $fileName;
                    
                    $controllerName::{$this->runLevel}($app);
                    
                }
                
            }
            
        }
        else {
            
            foreach ($this->registeredListeners as &$object) {
            
                $object->{$this->runLevel}();
            
            }
            
        }
    }
    
    public function updateRunLevel(AbstractApp &$app, $runLevel)
    {
        $this->runLevel = $runLevel;
        
        $this->processEvents($app);
        
        return $this;
    }
    
}