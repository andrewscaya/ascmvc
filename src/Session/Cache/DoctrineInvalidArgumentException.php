<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.1.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.1.0
 */

namespace Ascmvc\Session\Cache;

use Psr\Cache\InvalidArgumentException;

class DoctrineInvalidArgumentException extends \Exception implements InvalidArgumentException
{
    protected $argument = '';

    protected $message = 'An invalid argument was used with Doctrine Cache';

    public function __construct($argument)
    {
        $this->argument = $argument;
    }

    /**
     * @return mixed
     */
    public function getArgument()
    {
        return $this->argument;
    }
}