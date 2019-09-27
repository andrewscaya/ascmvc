<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.2
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.0.0
 */

namespace Ascmvc\EventSourcing;

/**
 * Class ReadModel
 *
 * @package Ascmvc\EventSourcing
 */
class ReadModel extends EventListener
{
    /**
     * Returns an instance of this class.
     *
     * @param EventDispatcher $eventDispatcher
     *
     * @return ReadModel
     */
    public static function getInstance(EventDispatcher $eventDispatcher)
    {
        return new self($eventDispatcher);
    }
}
