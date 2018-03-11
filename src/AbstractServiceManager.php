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

namespace Ascmvc;


/**
 * The abstract AbstractServiceManager class is the blueprint for the MVC's service manager.
 *
 * The AbstractServiceManager class is the one that needs to be extended
 * in order to create a LightMVC service manager.
 */
abstract class AbstractServiceManager {

    /**
     * Array contains the Service Manager's services objects.
     *
     * @var array|null
     */
    protected $registeredServices;
    
    /**
     * Array contains the Service Manager's ServiceManagerListenerInterface objects.
     *
     * @var array|null
     */
    protected $registeredListeners;
    
    /**
     * Contains a string that corresponds to the application's runlevel.
     *
     * @var string|null
     */
    protected $runLevel;
    
    /**
     * Registers a reference to a service object in the $registeredServices
     * array.
     *
     * @param string $serviceName.
     * 
     * @param mixed &$service
     *
     * @return AbstractServiceManager
     */
    public abstract function addRegisteredService($serviceName, &$service);
    
    /**
     * Removes a reference to a service object in the $registeredServices
     * array.
     *
     * @param string $serviceName
     *
     * @return AbstractServiceManager
     */
    public abstract function removeRegisteredService($serviceName);
    
    /**
     * Returns a service object that is registered in the $registeredServices
     * array.
     *
     * @param string $serviceName
     *
     * @return mixed
     */
    public abstract function getRegisteredService($serviceName);
    
    /**
     * Registers a reference to a ServiceManagerListenerInterface object
     * in the $registeredListeners array.
     *
     * @param string $listenerName
     * 
     * @param ServiceManagerListenerInterface
     *
     * @return AbstractServiceManager
     */
    public abstract function addRegisteredListener($listenerName, ServiceManagerListenerInterface &$listener);
    
    /**
     * Removes a reference to a ServiceManagerListenerInterface object
     * in the $registeredListeners array.
     *
     * @param string $listenerName.
     *
     * @return AbstractServiceManager
     */
    public abstract function removeRegisteredListener($listenerName);
    
    /**
     * Returns a ServiceManagerListenerInterface object that is registered
     * in the $registeredListeners array.
     *
     * @param string $listenerName.
     *
     * @return ServiceManagerListenerInterface
     */
    public abstract function getRegisteredListener($listenerName);
    
    /**
     * Runs the method corresponding to the new runlevel's name in each
     * ServiceManagerListenerInterface object that is registered
     * in the $registeredListeners array.
     *
     * @param AbstractApp &$app.
     *
     * @return void.
     */
    public abstract function processEvents(AbstractApp &$app);
    
    /**
     * Updates the $runLevel property with the new runlevel name
     * and calls $this->processEvents() method.
     *
     * @param AbstractApp &$app.
     * 
     * @param int $runLevel.
     *
     * @return AbstractServiceManager
     */
    public abstract function updateRunLevel(AbstractApp &$app, $runLevel);
    
}