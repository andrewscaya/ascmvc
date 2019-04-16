<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.1.1
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace Ascmvc;

use Ascmvc\Mvc\AscmvcEventManager;
use Pimple\Container;

/**
 * Interface AscmvcControllerFactoryInterface
 *
 * FactoryInterface allows the implementing class
 * to create itself using initialization logic.
 *
 * @package Ascmvc
 */
interface AscmvcControllerFactoryInterface
{

    /**
     * Allows an implementing object to initialize itself using
     * application resources and parameters.
     *
     * @param array $baseConfig
     * @param $viewObject
     * @param Container $serviceManager
     * @param AscmvcEventManager $eventManager
     * @return mixed
     */
    public static function factory(array &$baseConfig, &$viewObject, Container &$serviceManager, AscmvcEventManager &$eventManager);
}
