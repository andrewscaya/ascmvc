<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.0.0
 */

namespace Ascmvc\EventSourcing;

abstract class Command
{
    /**
     * Contains the command's arguments
     *
     * @var array
     */
    protected $args = [];

    /**
     * Command constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->args = $args;
    }

    abstract public function execute();
}
