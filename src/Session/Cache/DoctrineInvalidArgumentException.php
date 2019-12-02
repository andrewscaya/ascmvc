<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.3
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.1.0
 */

namespace Ascmvc\Session\Cache;

use Psr\Cache\InvalidArgumentException;

/**
 * Class DoctrineInvalidArgumentException
 *
 * @package Ascmvc\Session\Cache
 */
class DoctrineInvalidArgumentException extends \Exception implements InvalidArgumentException
{
    /**
     * Contains the argument that caused the exception.
     *
     * @var string
     */
    protected $argument = '';

    /**
     * DoctrineInvalidArgumentException constructor.
     *
     * @param $argument
     */
    public function __construct($argument)
    {
        $this->argument = $argument;
    }

    /**
     * Gets the argument that caused the exception.
     *
     * @return mixed
     */
    public function getArgument()
    {
        return $this->argument;
    }
}
