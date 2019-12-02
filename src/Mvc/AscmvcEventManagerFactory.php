<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.3
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace Ascmvc\Mvc;

use Zend\EventManager\SharedEventManager;

/**
 * Class AscmvcEventManagerFactory
 *
 * Returns an instance of the AscmvcEventManager without a shared manager.
 */
class AscmvcEventManagerFactory
{
    /**
     * Returns an instance of the AscmvcEventManager without a shared manager.
     *
     * @return AscmvcEventManager
     */
    public static function create() : AscmvcEventManager
    {
        $shared = new SharedEventManager();

        $eventManager = new AscmvcEventManager($shared);

        $eventManager->setIdentifiers(['AscmvcApplication']);

        return $eventManager;
    }
}
