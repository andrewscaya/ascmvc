<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.1
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.1.0
 */

namespace Ascmvc\EventSourcing;

use Ascmvc\Mvc\Controller;

/**
 * Class AggregateRootController
 *
 * @package Ascmvc\EventSourcing
 */
class AggregateRootController extends Controller
{
    /**
     * Contains the name of the Aggregate Root.
     *
     * @var string
     */
    protected $aggregateRootName;

    /**
     * Contains a list of listeners for this aggregate, where the key is the name of the event
     * and the value is the FQCN of the class that is to become a listener of the specified event.
     *
     * @var array
     */
    protected $aggregateListenerNames = [];

    /**
     * Controller constructor.
     *
     * @param array $baseConfig
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(array $baseConfig, EventDispatcher $eventDispatcher)
    {
        parent::__construct($baseConfig, $eventDispatcher);

        $this->aggregateRootName = static::class;

        $aggregateIdentifiers[] = $this->aggregateRootName;

        if (isset($baseConfig['eventlog']) && $baseConfig['eventlog']['enabled'] === true) {
            $aggregateIdentifiers[] = EventLogger::class;
        }

        $eventDispatcher->setIdentifiers($aggregateIdentifiers);

        if (!empty($this->aggregateListenerNames)) {
            foreach ($this->aggregateListenerNames as $key => $listenerName) {
                if (is_array($listenerName)) {
                    $keys = array_keys($listenerName);

                    $event = $keys[0];

                    $listener = $listenerName[$event];

                    $eventDispatcher->attach(
                        $event,
                        $listener::getInstance($eventDispatcher)
                    );
                }

                if (is_string($key) && is_string($listenerName)) {
                    $eventDispatcher->attach(
                        $key,
                        $listenerName::getInstance($eventDispatcher)
                    );
                }
            }
        }

        $sharedEventManager = $eventDispatcher->getSharedManager();

        if (!is_null($sharedEventManager)) {
            $sharedEventManager->attach(
                $this->aggregateRootName,
                '*',
                [$this, 'onAggregateEvent']
            );
        }
    }

    // @codeCoverageIgnoreStart
    /**
     * Runs before the controller's default action.
     *
     * @param null $vars
     *
     * @return mixed|void
     */
    public function preIndexAction($vars = null)
    {
    }
    // @codeCoverageIgnoreEnd
}
