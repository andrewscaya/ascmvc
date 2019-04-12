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
use Ascmvc\EventSourcing\Event\Event;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zend\EventManager\EventManager as ZendEventManager;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class EventDispatcher
 *
 * @package Ascmvc\EventSourcing
 */
class EventDispatcher extends ZendEventManager implements EventDispatcherInterface
{
    /**
     * The instance of the application.
     *
     * @var AbstractApp
     */
    protected $application;

    public function __construct(AbstractApp $application, SharedEventManagerInterface $sharedEventManager = null, array $identifiers = [])
    {
        parent::__construct($sharedEventManager, $identifiers);

        $this->application = $application;
    }

    /**
     * Dispatches the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function dispatch(object $event)
    {
        if ($event instanceof Event) {
            $event->setApplication($this->application);

            // Not checking if event is stopped because this event implementation IS NOT stoppable.
            $this->triggerEvent($event);
        }
    }
}
