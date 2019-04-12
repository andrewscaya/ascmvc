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

use Ascmvc\AbstractApp;
use Zend\EventManager\Event as ZendEvent;

class Event extends ZendEvent
{
    /**
     * The instance of the application.
     *
     * @var AbstractApp
     */
    protected $application;

    /**
     * Returns the instance of the application.
     *
     * @return AbstractApp
     */
    public function getApplication() : AbstractApp
    {
        return $this->application;
    }

    /**
     * Stores the instance of the application.
     *
     * @param AbstractApp $application
     *
     * @return AbstractApp
     */
    public function setApplication(AbstractApp &$application) : AbstractApp
    {
        $this->application = $application;
        return $this->application;
    }

    /**
     * Event propagation is not stoppable
     *
     * @param  bool $flag
     */
    public function stopPropagation($flag = true)
    {
        return false;
    }

    /**
     * Propagation is never stopped
     *
     * @return bool
     */
    public function propagationIsStopped()
    {
        return false;
    }
}
