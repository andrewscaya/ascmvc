<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.1
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.0.0
 */

namespace Ascmvc\EventSourcing;

use Ascmvc\AbstractApp;
use Ascmvc\EventSourcing\Event\Event;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager as ZendEventManager;
use Zend\EventManager\Exception;
use Zend\EventManager\ResponseCollection;
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

    /**
     * EventDispatcher constructor.
     *
     * @param AbstractApp $application
     * @param SharedEventManagerInterface|null $sharedEventManager
     * @param array $identifiers
     */
    public function __construct(AbstractApp $application, SharedEventManagerInterface $sharedEventManager = null, array $identifiers = [])
    {
        parent::__construct($sharedEventManager, $identifiers);

        $this->application = $application;
    }

    /**
     * Dispatches the event.
     *
     * @param object $event
     */
    public function dispatch(object $event)
    {
        if ($event instanceof Event) {
            $event->setApplication($this->application);

            // Not checking if event is stopped because this event implementation IS NOT stoppable.
            $this->triggerEvent($event);
        }
    }

    // @codeCoverageIgnoreStart

    /**
     * Triggers the given event.
     *
     * @param EventInterface $event
     * @return ResponseCollection
     */
    public function triggerEvent(EventInterface $event)
    {
        return $this->triggerListeners($event);
    }
    // @codeCoverageIgnoreEnd

    /**
     * Trigger listeners
     *
     * Actual functionality for triggering listeners, to which trigger() delegate.
     *
     * @param  EventInterface $event
     * @param  null|callable $callback
     * @return ResponseCollection
     */
    protected function triggerListeners(EventInterface $event, callable $callback = null)
    {
        // @codeCoverageIgnoreStart
        $name = $event->getName();

        if (empty($name)) {
            throw new Exception\RuntimeException('Event is missing a name; cannot trigger!');
        }

        if (isset($this->events[$name])) {
            $listOfListenersByPriority = $this->events[$name];

            if (isset($this->events['*'])) {
                foreach ($this->events['*'] as $priority => $listOfListeners) {
                    $listOfListenersByPriority[$priority][] = $listOfListeners[0];
                }
            }
        } elseif (isset($this->events['*'])) {
            $listOfListenersByPriority = $this->events['*'];
        } else {
            $listOfListenersByPriority = [];
        }

        if ($this->sharedManager) {
            foreach ($this->sharedManager->getListeners($this->identifiers, $name) as $priority => $listeners) {
                $listOfListenersByPriority[$priority][] = $listeners;
            }
        }

        // Sort by priority in reverse order
        krsort($listOfListenersByPriority);

        // Initial value of stop propagation flag should be false
        $event->stopPropagation(false);

        // Execute listeners
        $responses = new ResponseCollection();
        foreach ($listOfListenersByPriority as $listOfListeners) {
            foreach ($listOfListeners as $listeners) {
                $asyncListeners = [];
                foreach ($listeners as $listener) {
                    if (is_callable($listener) && is_object($listener)) {
                        $asyncListeners[] = $listener($event);
                    } else {
                        $response = $listener($event);
                        $responses->push($response);

                        // If the event was asked to stop propagating, do so
                        if ($event->propagationIsStopped()) {
                            $responses->setStopped(true);
                            return $responses;
                        }

                        // If the result causes our validation callback to return true,
                        // stop propagation
                        if ($callback && $callback($response)) {
                            $responses->setStopped(true);
                            return $responses;
                        }
                    }
                }
                // @codeCoverageIgnoreEnd

                while (true) {
                    foreach ($asyncListeners as $key => $asyncListener) {
                        if ($asyncListener->current() !== null) {
                            if ($asyncListener->current() === true) {
                                $asyncListener->next();
                            }
                        }

                        if (!$asyncListener->valid()) {
                            unset($asyncListeners[$key]);

                            $responses->push(true);
                        }
                    }

                    if (empty($asyncListeners)) {
                        break;
                    }
                }
            }
        }

        return $responses;
    }
}
