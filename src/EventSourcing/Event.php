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

abstract class Event extends ZendEvent
{
    /**
     * Contains the elements of the event's circumstances (amounts, values, places, etc.)
     *
     * @var array
     */
    protected $circumstances = [];

    /**
     * The instance of the application.
     *
     * @var AbstractApp
     */
    protected $application;

    public function __construct(array $circumstances)
    {
        $this->circumstances = $circumstances;
        $this->saveEvent($this->getName(), $this->circumstances);
    }

    /**
     * Saves the event name and the event circumstances to the persistence layer.
     *
     * @param string $name
     * @param array $circumstances
     * @return mixed
     */
    abstract protected function saveEvent(string $name, array $circumstances);

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
     * @return AbstractApp
     */
    public function setApplication(AbstractApp &$application) : AbstractApp
    {
        $this->application = $application;
        return $this->application;
    }
}
