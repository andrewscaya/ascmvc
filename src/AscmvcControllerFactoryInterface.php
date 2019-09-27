<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.2
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace Ascmvc;

use Ascmvc\EventSourcing\EventDispatcher;
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
     * @param EventDispatcher $eventDispatcher
     * @param Container $serviceManager
     * @param $viewObject
     * @return mixed
     */
    public static function factory(array &$baseConfig, EventDispatcher &$eventDispatcher, Container &$serviceManager, &$viewObject);
}
