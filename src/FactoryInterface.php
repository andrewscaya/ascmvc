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

use Pimple\Container;

/**
 * FactoryInterface allows the implementing class
 * to create itself using initialization logic.
 */
interface FactoryInterface {
    
    /**
     * Allows an implementing object to initialize itself using
     * application resources and parameters.
     *
     * @param array &$baseConfig
     * @param &$viewObject
     * @param Container &$serviceManager
     * @param AbstractEventManager &$eventManager
     *
     * @return void
     */
    public static function factory(array &$baseConfig, &$viewObject, Container &$serviceManager, AbstractEventManager &$eventManager);
    
}
