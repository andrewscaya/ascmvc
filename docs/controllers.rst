.. _ControllersAnchor:

.. index:: Controllers

.. _controllers:

Controllers
===========

The framework's controllers are extensions of the ``Ascmvc\Mvc\Controller`` or the
``Ascmvc\EventSourcing\AggregateRootController`` classes, which both implement
the ``Ascmvc\AscmvcEventManagerListenerInterface`` interface. The ``AggregateRootController`` also implements
the ``Ascmvc\EventSourcing\AggregateEventListenerInterface``. Within the LightMVC Framework, controllers
are considered to be the Aggregate Root (main command) of the each and every event sourcing aggregate.

.. note:: For more information on configuring an application's event sourcing aggregates and the application's event log, please see the :ref:`configuration eventsourcing` section.

.. note:: For more information on the framework's event sourcing aggregates in general, please see the :ref:`eventsourcing` section.

.. index:: Controller methods

.. index:: Request handler methods

.. _controller methods:

Controller Methods
------------------

Every controller has the following basic concrete definition:

.. code-block:: php

    class Controller extends AbstractController implements AscmvcEventManagerListenerInterface
    {
        public function __construct(array $baseConfig)
        {
            $this->baseConfig = $baseConfig;

            $this->view = $this->baseConfig['view'];
        }

        public static function onBootstrap(AscmvcEvent $event)
        {
        }

        public function onDispatch(AscmvcEvent $event)
        {
        }

        public function onRender(AscmvcEvent $event)
        {
        }

        public function onFinish(AscmvcEvent $event)
        {
        }

        public function indexAction($vars = null)
        {
        }
    }

Thus, every controller has an ``indexAction`` request handler by default, and every controller has the
ability to tap into any of the framework's major events, except the ``AscmvcEvent::EVENT_ROUTE`` event.
Upon instantiation of the required controller by the controller manager (dispatcher),
a minimal version of the application's ``$baseConfig`` array will be injected into the controller. Upon
execution of the controller's request handler method, all the global server variables are injected into
the handler through the ``$vars`` variable.

.. note:: One should avoid as much as possible to use the onBootstrap() method within the controller classes, as this would not scale very well if there is a large number of controllers.

For more information on the event manager and the main MVC events, please see the :ref:`event manager` section.

When extending the ``Ascmvc\EventSourcing\AggregateRootController`` class instead of the
``Ascmvc\Mvc\Controller`` class, a controller has the following additional concrete definition:

.. code-block:: php

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
    }

Essentially, these additional facilities allow for automatic configuration of the Aggregate Root,
by setting the name of the Aggregate Root, by setting the Event Dispatcher's identifiers accordingly,
by attaching all listeners found in the ``$aggregateListenerNames`` property to the specified events,
and by attaching the Aggregate Root controller as a listener to all events dispatched within its own
event sourcing aggregate. If event logging is enabled, it will also add the Event Logger's Aggregate Root
name as an aggregate identifier in order to dispatch all of the current aggregate's events to this other
aggregate. Adding more identifiers might also prove useful in case one needs to also dispatch all events
from one aggregate to another.

Additionally, the 'pre' action methods allow for the dispatching of events before the actual call to the
main action method. The naming convention for 'pre' methods is to capitalize the first letter of the name
of the action method and to add the prefix 'pre' in front of the name. Thus, the ``indexAction()`` method
would have a 'pre' action method with the name ``preIndexAction()``. The 'pre' method has access to the same
environment variables as the controller's main request handler method, through the injection of the
``$vars`` variable.

.. note:: For further reading on the framework's event sourcing aggregates in general, please see the :ref:`eventsourcing` section.

.. index:: Controller factories

.. index:: Controller factory interface

.. index:: Controller Manager

.. _controller factories:

Controller Factories
--------------------

Any controller can implement the ``Ascmvc\AscmvcControllerFactoryInterface`` interface and become a
factory that can store a factory of itself in the service manager (**Pimple** container) and/or return
an instance of itself to the controller manager, after completing some specific logic.

This is useful if you need to set up some specific service or resource before injecting it into an instance
of the controller, or if you need to customize the way the event sourcing aggregates and listeners are
configured and set up in general by overriding the ``Ascmvc\EventSourcing\AggregateRootController`` class'
automatic configuration.

For a working example, please see the section on the :ref:`skeleton`.

For information on how to deal with other types of factories, please see the :ref:`service manager` section.