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


/**
 * The abstract AbstractServiceManager class is the blueprint for the MVC's service manager.
 *
 * *Description* The AbstractServiceManager class is the one that needs to be extended
 * in order to create a LightMVC service manager.
 */
abstract class AbstractServiceManager {

    /**@var array|null  Array contains the Service Manager's services objects.*/
    protected $registeredServices;
    
    /**@var array|null  Array contains the Service Manager's ServiceManagerListenerInterface objects.*/
    protected $registeredListeners;
    
    /**@var string|null  Contains a string that is name of the application.*/
    protected $runLevel;
    
    /**
     * Registers a reference to a service object in the $registeredServices
     * array.
     *
     * @param string $serviceName.
     * 
     * @param Object &$service.
     *
     * @return Object:AbstractServiceManager $this.
     */
    public abstract function addRegisteredService($serviceName, &$service);
    
    /**
     * Removes a reference to a service object in the $registeredServices
     * array.
     *
     * @param string $serviceName.
     *
     * @return Object:AbstractServiceManager $this.
     */
    public abstract function removeRegisteredService($serviceName);
    
    /**
     * Returns a service object that is registered in the $registeredServices
     * array.
     *
     * @param string $serviceName.
     *
     * @return Object $this->registeredServices[$serviceName].
     */
    public abstract function getRegisteredService($serviceName);
    
    /**
     * Registers a reference to a ServiceManagerListenerInterface object
     * in the $registeredListeners array.
     *
     * @param string $listenerName.
     * 
     * @param Object:ServiceManagerListenerInterface &$listener.
     *
     * @return Object:AbstractServiceManager $this.
     */
    public abstract function addRegisteredListener($listenerName, ServiceManagerListenerInterface &$listener);
    
    /**
     * Removes a reference to a ServiceManagerListenerInterface object
     * in the $registeredListeners array.
     *
     * @param string $listenerName.
     *
     * @return Object:AbstractServiceManager $this.
     */
    public abstract function removeRegisteredListener($listenerName);
    
    /**
     * Returns a ServiceManagerListenerInterface object that is registered
     * in the $registeredListeners array.
     *
     * @param string $listenerName.
     *
     * @return Object $this->registeredListeners[$listenerName].
     */
    public abstract function getRegisteredListener($listenerName);
    
    /**
     * Runs the method corresponding to the new runlevel's name in each
     * ServiceManagerListenerInterface object that is registered
     * in the $registeredListeners array.
     *
     * @param Object:App &$app.
     *
     * @return void.
     */
    public abstract function processEvents(App &$app);
    
    /**
     * Updates the $runLevel property with the new runlevel name
     * and calls $this->processEvents() method.
     *
     * @param Object:App &$app.
     * 
     * @param string $runLevel.
     *
     * @return Object:AbstractServiceManager $this.
     */
    public abstract function updateRunLevel(App &$app, $runLevel);
    
}