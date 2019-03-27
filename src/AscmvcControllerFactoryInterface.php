<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.2
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace Ascmvc;

use Ascmvc\EventSourcing\EventProcessor;
use Pimple\Container;
use Ascmvc\Mvc\AscmvcEventManager;

/**
 * FactoryInterface allows the implementing class
 * to create itself using initialization logic.
 */
interface AscmvcControllerFactoryInterface
{

    /**
     * Allows an implementing object to initialize itself using
     * application resources and parameters.
     *
     * @param array &$baseConfig
     * @param &$viewObject
     * @param Container &$serviceManager
     * @param AscmvcEventManager &$eventManager
     *
     */
    public static function factory(array &$baseConfig, EventProcessor &$eventProcessor, Container &$serviceManager, &$viewObject);
}
