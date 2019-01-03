<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.0
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.0.0
 */

namespace Ascmvc;

/**
 * EventManagerListenerInterface allows the implementing class
 * to be consumed as a AscmvcEventManager class listener.
 *
 * The interface's methods correspond exactly to the
 * App Class' runlevels as they are defined in its run() method
 * so that, in turn, these methods may be dynamically called by the
 * EventManager's event-driven triggerEvent() method.
 */
interface AscmvcEventManagerListenerInterface extends
    AscmvcBootstrapListenerInterface,
    AscmvcDispatchListenerInterface,
    AscmvcRenderListenerInterface,
    AscmvcFinishListenerInterface
{

}
