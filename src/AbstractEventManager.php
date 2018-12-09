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
 * The abstract AbstractEventManager class is the blueprint for the MVC's event manager.
 *
 * The AbstractEventManager class is the one that needs to be extended
 * in order to create a LightMVC event manager.
 */
abstract class AbstractEventManager {
    
    /**
     * Array contains the Event Manager's EventManagerListenerInterface objects.
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
     * Registers a reference to a EventManagerListenerInterface object
     * in the $registeredListeners array.
     *
     * @param string $listenerName
     * 
     * @param EventManagerListenerInterface
     *
     * @return AbstractEventManager
     */
    public abstract function addRegisteredListener($listenerName, EventManagerListenerInterface &$listener);
    
    /**
     * Removes a reference to a EventManagerListenerInterface object
     * in the $registeredListeners array.
     *
     * @param string $listenerName.
     *
     * @return AbstractEventManager
     */
    public abstract function removeRegisteredListener($listenerName);
    
    /**
     * Returns a EventManagerListenerInterface object that is registered
     * in the $registeredListeners array.
     *
     * @param string $listenerName.
     *
     * @return EventManagerListenerInterface
     */
    public abstract function getRegisteredListener($listenerName);
    
    /**
     * Runs the method corresponding to the new runlevel's name in each
     * EventManagerListenerInterface object that is registered
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
     * @return AbstractEventManager
     */
    public abstract function updateRunLevel(AbstractApp &$app, $runLevel);
    
}
