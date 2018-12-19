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

use Ascmvc\Mvc\AscmvcEvent;


/**
 * AscmvcRenderListenerInterface allows the implementing class
 * to be consumed as a AscmvcEventManager class listener.
 *
 * The interface's methods correspond exactly to the
 * App Class' events as they are used in its run() method
 * so that, in turn, these methods may be dynamically called by the
 * EventManager's event-driven triggerEvent() method.
 */
interface AscmvcRouteListenerInterface {

    /**
     * Allows an implementing object to interrupt the App's runtime when the
     * FastRouter resolves the route.
     *
     * @param AscmvcEvent $event
     *
     * @return Response|void
     */
    public static function onRoute(AscmvcEvent $event);
    
}
