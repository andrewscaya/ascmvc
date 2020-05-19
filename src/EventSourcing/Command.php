<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    4.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.0.0
 */

namespace Ascmvc\EventSourcing;

/**
 * Class Command
 *
 * @package Ascmvc\EventSourcing
 */
abstract class Command
{
    /**
     * Contains an instance of the \Ascmvc\EventSourcing\EventDispatcher
     *
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * Contains an array of the command's arguments
     *
     * @var array
     */
    protected $argv = [];

    /**
     * Command constructor.
     *
     * @param EventDispatcher $eventDispatcher
     * @param array $argv
     */
    public function __construct(EventDispatcher $eventDispatcher, array $argv)
    {
        $this->eventDispatcher = $eventDispatcher;

        $this->argv = $argv;
    }

    /**
     * The command's main runtime execution method
     *
     * @return mixed
     */
    abstract public function execute();
}
