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

use Pimple\Container;
use Zend\EventManager\EventManager;

/**
 * FactoryInterface allows the implementing class
 * to create itself using initialization logic.
 */
interface FactoryInterface
{

    /**
     * Allows an implementing object to initialize itself using
     * application resources and parameters.
     *
     * @param array &$baseConfig
     * @param &$viewObject
     * @param Container &$serviceManager
     * @param EventManager &$eventManager
     *
     * @return void
     */
    public static function factory(array &$baseConfig, &$viewObject, Container &$serviceManager, EventManager &$eventManager);
}
