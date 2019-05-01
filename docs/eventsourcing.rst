.. _EventsourcingAnchor:

.. index:: Event Sourcing

.. _eventsourcing:

Event Sourcing
==============

The framework's event sourcing library allows you to set up an event sourcing infrastructure, that uses
CQRS or not, based on controllers as the aggregate root (main command and main event listener) of
each and every aggregate. The principle of it is that all of the software's underlying commands and
interactions with other systems (internal or external) will be called through the dispatching of events.
These events are named 'aggregate events' and can be any custom event that the controller can dispatch
through an event dispatcher, also known as the event bus. The event dispatcher will then notify
any listener that is attached to this event.

Typically, an event sourcing aggregate will have Read Model and Policy listeners. These event
listeners will then call aggregate commands by passing any values received through the aggregate event.
In order to make things more simple, these values are normally wrapped inside an immutable value object
that allows for a unified interface to deal with these values throughout the entire aggregate life cycle.

Since all of the aggregate's underlying commands are called through events, logging the aggregate's activities
is made much more simple. Auditing and monitoring are added benefits that come with event sourcing.

To read further on event sourcing, please see the
`Martin Fowler's definition and explanations on this subject <https://martinfowler.com/eaaDev/EventSourcing.html>`_.

.. note:: For more information on configuring an application's event sourcing aggregates and the application's event log, please see the :ref:`configuration eventsourcing` section.

.. index:: Event Sourcing Aggregates

.. index:: Event Sourcing Root Aggregates

.. _eventsourcing aggregates:

Aggregates
----------

An aggregate is defined as a collective of classes that work together in order to accomplish a specific task.
When using aggregates with controllers as their aggregate root, this task, or common goal,
is sending a response back for a specific request from the client.

Thus, the first step in order to start using aggregates is to define a new controller by extending the
``Ascmvc\EventSourcing\AggregateRootController`` class. This allows for automatic configuration of the
aggregate root. Essentially, it will set the name of the Aggregate Root, set  the Event Dispatcher's
identifiers accordingly, attach all listeners found in the class' ``$aggregateListenerNames`` property,
and attach the Aggregate Root controller as a listener to all events dispatched within the event sourcing
aggregate. If event logging is enabled, it will also add the Event Logger's Aggregate Root name as an
aggregate identifier in order to dispatch all of the current aggregate's events to this other aggregate.

.. note:: For more information on this automatic configuration of the aggregate root, please see the section on :ref:`controller methods`.

It is also posible to define an aggregate manually, by extending the ``Ascmvc\Mvc\Controller`` class
and by establishing the aggregate root from within a controller's ``factory()`` method when implementing the
``\Ascmvc\AscmvcControllerFactoryInterface`` interface, like so:

.. code-block:: php

    public static function factory(array &$baseConfig, EventDispatcher &$eventDispatcher, Container &$serviceManager, &$viewObject)
    {
        // Setting the identifiers of this Event Dispatcher (event bus).
        // Subscribing this controller (Aggregate Root) and the Event Sourcing Logger.
        $eventDispatcher->setIdentifiers(
            [
                SomeController::class,
            ]
        );

        // Do something else...

        $controller = new SomeController($baseConfig, $eventDispatcher);

        $sharedEventManager = $eventDispatcher->getSharedManager();

        // Attaching this controller's listener method to the shared event manager's
        // corresponding identifier (see above).
        $sharedEventManager->attach(
            SomeController::class,
            '*',
            [$controller, 'onAggregateEvent']
        );

        return $controller;
    }

By setting the event dispatcher's identifier to the controller class' fully-qualified class name (FQCN)
and by attaching the controller's listener method ``onAggregateEvent`` with the controller's
name as the aggregate root's name (first parameter of the event dispatcher's ``attach()`` method) and with a
wildcard symbol as the event' name (second parameter of the same ``attach()`` method), we are, in fact,
making this controller a listener to all of the aggregates events. This will allow the controller to determine
what is left to be done, before a response can be considered to be completely finished. In
an asynchronous environment like Swoole, this allows for simultaneous execution of multiple parts of the
aggregate, without having to wait for one part to finish before another one can be executed.

Each part of the aggregate is then responsible of accomplishing its own subordinated task in order
to fulfill the common goal. The way each part of the aggregate can interact with the other parts is by
dispatching events through the event dispatcher.

.. index:: Event Sourcing Dispatcher

.. index:: Event Sourcing Bus

.. _eventsourcing dispatcher:

Event Dispatcher
----------------

The default LightMVC event dispatcher is an instance of the ``\Ascmvc\EventSourcing\EventDispatcher`` class.
It is a PSR-14 compliant event dispatcher. Therefore, you can replace this event dispatcher with any other
PSR-14 compliant event dispatcher. Since the LightMVC event dispatcher is an extension of the
``\Zend\EventManager\EventManager``, it is possible to use any of the known Zend event manager facilities.

.. note:: For more information on configuring an application's event sourcing aggregates, please see the :ref:`configuration eventsourcing` section.

To dispatch aggregate events, it is a question of instantiating an aggregate value object and an
aggregate event, and then using the event dispatcher's ``dispatch()`` method to dispatch it to the
attached listeners.

.. code-block:: php

    // The value object can be empty.
    $aggregateValueObject = new AggregateImmutableValueObject();

    // The aggregate even must receive an aggregate value object,
    // the name of aggregate root, and the name of the event.
    $event = new AggregateEvent(
        $aggregateValueObject,
        ProductsController::class,
        ProductsController::READ_REQUESTED
    );

    $this->eventDispatcher->dispatch($event);

.. note:: If listeners are callables that return a ``\Generator`` instance, the Event Dispatcher will run these listeners asynchronously, with the lowest possible priority. This is very useful for long-lasting tasks that need to be executed as quickly as possible.

.. note:: Default aggregate listeners that are configured automatically by the aggregate root controller, using the ``$aggregateListenerNames`` property, MUST be invokable objects.

The event dispatcher contains an instance of the ``\Zend\EventManager\SharedEventManager`` by default. This
allows for the dispatching of events to other parts of the application, or for listening to events dispatched
by other parts of the application.

For more information on the shared event manager, please see the :ref:`event manager` section.

.. index:: Event Sourcing Aggregate Events

.. _eventsourcing events:

Aggregate Events
----------------

The LightMVC ``\Ascmvc\EventSourcing\Event\AggregateEvent`` class is, ultimately, an extension of the
``\Zend\EventManager\Event`` class. The added facilities allow the dispatching code to define the name
of the aggregate root, and to inject an aggregate value object to be shared with listeners. The framework
defines two child event classes: ``\Ascmvc\EventSourcing\Event\ReadAggregateCompletedEvent`` and
``\Ascmvc\EventSourcing\Event\WriteAggregateCompletedEvent``. These two classes are designed to make logging
easier and to allow for dispatching to the Read Model and Policy listeners more convenient.

.. index:: Event Sourcing Values Objects

.. _eventsourcing valueobjects:

Aggregate Value Objects
-----------------------

An ``\Ascmvc\EventSourcing\AggregateImmutableValueObject`` object is an immutable value object that is
designed to allow all parts of an aggregate to easily share any data through a common interface. An aggregate
value object can be empty. Since this class implements the \Serializable interface, it is possible to
serialize its data into a string format. Finally, it allows its data to be hydrated into an array with its
``hydrateToArray()`` method.

.. index:: Event Sourcing Event Aggregate Listeners

.. _eventsourcing listeners:

Aggregate Event Listeners
-------------------------

All LightMVC listeners implement the ``\Ascmvc\EventSourcing\EventListenerInterface`` interface. This interface
defines one single listener method named ``onEvent()``. This being said, one can define any custom listener
method, but the LightMVC event sourcing implementation recommends using the default ``onEvent()`` listener
method for all event listeners. The framework offers an implementation of this interface which is named
``\Ascmvc\EventSourcing\EventListener``. This class allows the extending listener object to benefit from the
automatic injection of the controller's Event Dispatcher.

Moreover, the framework defines an ``\Ascmvc\EventSourcing\AggregateEventListenerInterface`` interface, that
has an ``onAggregateEvent()`` listener method. The implementing class is named
``\Ascmvc\EventSourcing\AggregateEventListener`` and allows for automatic configuration of the event listener's
``$aggregateRootName`` property, depending on the name of the aggregate that dispatched the event.

There are two main types of listeners in the LightMVC event sourcing implementation. The
``\Ascmvc\EventSourcing\ReadModel`` class and the ``\Ascmvc\EventSourcing\Policy`` class. The framework also
offers variants of these two main types when dealing with aggregates: the
``\Ascmvc\EventSourcing\AggregateReadModel`` and the ``\Ascmvc\EventSourcing\AggregatePolicy`` classes.

.. index:: Event Sourcing Read Models

.. _eventsourcing readmodel:

Aggregate Read Models
---------------------

The ``\Ascmvc\EventSourcing\ReadModel`` class, or the ``\Ascmvc\EventSourcing\AggregateReadModel`` variant for
aggregates, are to be used to call a command that will read data from a given source. The Read Model
is responsible of determining what is the data source and how to access it.

Here is an example of an invokable non-blocking Aggregate Read Model that calls an asynchronous read command through the
``\Ascmvc\EventSourcing\CommandRunner`` command bus:

.. code-block:: php

    use Application\Events\ReadProductsCompleted;
    use Application\Models\Entity\Products;
    use Application\Models\Traits\DoctrineTrait;
    use Ascmvc\EventSourcing\AggregateImmutableValueObject;
    use Ascmvc\EventSourcing\AggregateReadModel;
    use Ascmvc\EventSourcing\CommandRunner;
    use Ascmvc\EventSourcing\Event\AggregateEvent;
    use Ascmvc\EventSourcing\Event\Event;
    use Ascmvc\EventSourcing\EventDispatcher;

    class ProductsReadModel extends AggregateReadModel
    {
        const READ_COMPLETED = 'products_read_completed';

        use DoctrineTrait;

        protected $id;

        protected $products;

        protected $productsRepository;

        protected $commandRunner;

        protected function __construct(EventDispatcher $eventDispatcher, Products $products)
        {
            parent::__construct($eventDispatcher);

            $this->products = $products;
        }

        public static function getInstance(EventDispatcher $eventDispatcher)
        {
            $productsEntity = new Products();

            return new self($eventDispatcher, $productsEntity);
        }

        public function __invoke(AggregateEvent $event)
        {
            if (is_null($this->commandRunner)) {
                $this->onAggregateEvent($event);

                $app = $event->getApplication();

                $valuesArray = $event->getAggregateValueObject()->getProperties();

                $arguments = [];

                if (!empty($valuesArray)) {
                    $values = $event->getAggregateValueObject()->serialize();

                    $arguments = [
                        '--values' => $values,
                    ];
                }

                $swoole = $app->isSwoole();

                $this->commandRunner = new CommandRunner($app, 'products:read', $arguments, $swoole);
            }

            while ($this->commandRunner->start()) {
                yield true;
            }

            $processStdout = $this->commandRunner->getOutput();
            //$processStderr = $this->commandProcess->getError();

            $aggregateValueObject = new AggregateImmutableValueObject();

            if (!empty(trim($processStdout))) {
                $aggregateValueObject = $aggregateValueObject->unserialize($processStdout);
            }

            $event = new ReadProductsCompleted(
                $aggregateValueObject,
                $event->getAggregateRootName(),
                ProductsReadModel::READ_COMPLETED
            );

            $this->eventDispatcher->dispatch($event);

            return;
        }

        public function onAggregateEvent(AggregateEvent $event)
        {
            parent::onAggregateEvent($event);
        }

        public function onEvent(Event $event)
        {
        }
    }

Here is another example of a Read Model that calls a blocking read command, by passing to it all the
necessary data, and the required database entity manager, in order for the command to successfully
execute itself and retrieve data from a 'products' table in the database:

.. code-block:: php

    <?php

    namespace Application\ReadModels;

    use Application\Commands\ReadProductsCommand;
    use Application\Models\Entity\Products;
    use Application\Models\Traits\DoctrineTrait;
    use Ascmvc\EventSourcing\Event\Event;
    use Ascmvc\EventSourcing\EventDispatcher;
    use Ascmvc\EventSourcing\ReadModel;

    class ProductsReadModel extends ReadModel
    {
        use DoctrineTrait;

        protected $id;

        protected $products;

        protected $productsRepository;

        protected function __construct(EventDispatcher $eventDispatcher, Products $products)
        {
            parent::__construct($eventDispatcher);

            $this->products = $products;
        }

        public static function getInstance(EventDispatcher $eventDispatcher)
        {
            $productsEntity = new Products();

            return new self($eventDispatcher, $productsEntity);
        }

        public function onEvent(Event $event)
        {
            // The read connection can be different from the write connection if implementing full CQRS.
            $connName = $event->getApplication()->getBaseConfig()['events']['read_conn_name'];

            $entityManager = $event->getApplication()->getServiceManager()[$connName];

            $productsCommand = new ReadProductsCommand(
                $event->getAggregateValueObject(),
                $entityManager,
                $this->eventDispatcher
            );

            if (!is_null($productsCommand)) {
                $productsCommand->execute();
            }

            return;
        }
    }

If the listener is named inside the ``Ascmvc\EventSourcing\AggregateRootController`` class'
``$aggregateListenerNames`` array property, it will automatically be called upon when the specified event
occurs.

If configuring the aggregate manually, one must, from within the controller's ``factory()`` method
(or any other main ``AscmvcEvent`` method), attach the Read Model to the aggregate's
event bus (event dispatcher) in this way:

.. code-block:: php

    // Controller's factory() method

    // Manually attach an invokable listeners if needed
    $someReadModel = SomeReadModel::getInstance($eventDispatcher);

    $eventDispatcher->attach(
        ProductsController::READ_REQUESTED,
        $someReadModel
    );

Thus, the Read Model will listen for any event with the name ``ProductsController::READ_REQUESTED`` from
within this aggregate.

.. index:: Event Sourcing Policies

.. _eventsourcing policies:

Aggregate Policies
------------------

The ``\Ascmvc\EventSourcing\Policy`` class, or the ``\Ascmvc\EventSourcing\AggregatePolicy`` class, are
to be used to call a command that will write data to a given source. The Policy is responsible of
determining what data to write, where to store it and how to access the storage.

Here is an example of an invokable non-blocking Aggregate Policy that calls an asynchronous write command
through the ``\Ascmvc\EventSourcing\CommandRunner`` command bus and then, dispatches a new event by
including the output from the command:

.. code-block:: php

    use Application\Controllers\ProductsController;
    use Application\Events\WriteProductsCompleted;
    use Application\Models\Traits\DoctrineTrait;
    use Ascmvc\EventSourcing\AggregateImmutableValueObject;
    use Ascmvc\EventSourcing\AggregatePolicy;
    use Ascmvc\EventSourcing\CommandRunner;
    use Ascmvc\EventSourcing\Event\AggregateEvent;
    use Ascmvc\EventSourcing\Event\Event;
    use Ascmvc\EventSourcing\EventDispatcher;

    class ProductsPolicy extends AggregatePolicy
    {
        const CREATE_COMPLETED = 'products_create_completed';

        const UPDATE_COMPLETED = 'products_update_completed';

        const DELETE_COMPLETED = 'products_delete_completed';

        use DoctrineTrait;

        protected $properties;

        protected $products;

        protected $productsRepository;

        protected $commandRunner;

        public static function getInstance(EventDispatcher $eventDispatcher)
        {
            return new self($eventDispatcher);
        }

        public function __invoke(AggregateEvent $event)
        {
            if (is_null($this->commandRunner)) {
                $this->onAggregateEvent($event);

                $app = $event->getApplication();

                $name = $event->getName();

                $execute = '';

                if ($name === ProductsController::CREATE_REQUESTED) {
                    $execute = 'create';
                } elseif ($name === ProductsController::UPDATE_REQUESTED) {
                    $execute = 'update';
                } elseif ($name === ProductsController::DELETE_REQUESTED) {
                    $execute = 'delete';
                }

                $valuesArray = $event->getAggregateValueObject()->getProperties();

                $arguments = [];

                if (!empty($valuesArray)) {
                    $values = $event->getAggregateValueObject()->serialize();

                    $arguments = [
                        'execute' => $execute,
                        '--values' => $values,
                    ];
                }

                $swoole = $app->isSwoole();

                $this->commandRunner = new CommandRunner($app, 'products:write', $arguments, $swoole);
            }

            while ($this->commandRunner->start()) {
                yield true;
            }

            $processStdout = $this->commandRunner->getOutput();
            //$processStderr = $this->commandProcess->getError();

            if (!empty($processStdout)) {
                $processStdoutArray = unserialize($processStdout);

                if (isset($processStdoutArray['data'])) {
                    $valueObjectProperties = $processStdoutArray['data'];
                }
            } else {
                $valueObjectProperties = [];
            }

            $name = $event->getName();

            $aggregateValueObject = new AggregateImmutableValueObject($valueObjectProperties);

            if ($name === ProductsController::CREATE_REQUESTED) {
                $event = new WriteProductsCompleted(
                    $aggregateValueObject,
                    $event->getAggregateRootName(),
                    ProductsPolicy::CREATE_COMPLETED
                );
            } elseif ($name === ProductsController::UPDATE_REQUESTED) {
                $event = new WriteProductsCompleted(
                    $aggregateValueObject,
                    $event->getAggregateRootName(),
                    ProductsPolicy::UPDATE_COMPLETED
                );
            } elseif ($name === ProductsController::DELETE_REQUESTED) {
                $event = new WriteProductsCompleted(
                    $aggregateValueObject,
                    $event->getAggregateRootName(),
                    ProductsPolicy::DELETE_COMPLETED
                );
            }

            $eventParams = $processStdoutArray['params'];

            $event->setParams($eventParams);

            $this->eventDispatcher->dispatch($event);

            return;
        }

        public function onAggregateEvent(AggregateEvent $event)
        {
            parent::onAggregateEvent($event);
        }

        public function onEvent(Event $event)
        {
        }
    }

Here is another example of a Policy that calls a blocking write command, by passing to it all the necessary
data, and the required database entity manager, in order for the command to successfully execute itself
and store the data to a 'products' table in the database:

.. code-block:: php

    <?php

    namespace Application\Policies;

    use Application\Commands\WriteProductsCommand;
    use Application\Models\Traits\DoctrineTrait;
    use Ascmvc\EventSourcing\Event\Event;
    use Ascmvc\EventSourcing\EventDispatcher;
    use Ascmvc\EventSourcing\Policy;

    class ProductsPolicy extends Policy
    {
        use DoctrineTrait;

        protected $properties;

        protected $products;

        protected $productsRepository;

        public static function getInstance(EventDispatcher $eventDispatcher)
        {
            return new self($eventDispatcher);
        }

        public function onEvent(Event $event)
        {
            $connName = $event->getApplication()->getBaseConfig()['events']['write_conn_name'];

            $entityManager = $event->getApplication()->getServiceManager()[$connName];

            $argv['name'] = $event->getName();

            $productsCommand = new WriteProductsCommand(
                $event->getAggregateValueObject(),
                $entityManager,
                $this->eventDispatcher,
                $argv
            );

            $productsCommand->execute();

            return;
        }
    }

If the listener is named inside the ``Ascmvc\EventSourcing\AggregateRootController`` class'
``$aggregateListenerNames`` array property, it will automatically be called upon when the specified event
occurs.

If configuring the aggregate manually, one must, from within the controller's ``factory()`` method
(or any other main ``AscmvcEvent`` method), attach the Read Model to the aggregate's
event bus (event dispatcher) in this way:

.. code-block:: php

    // Controller's factory() method

    $productsPolicy = ProductsPolicy::getInstance($eventDispatcher);

    // If there are many listeners to attach, one may use a
    // Listener Aggregate that implements the \Zend\EventManager\ListenerAggregateInterface
    // instead of attaching them one by one.
    $eventDispatcher->attach(
        ProductsController::CREATE_REQUESTED,
        $somePolicy
    );

    $eventDispatcher->attach(
        ProductsController::UPDATE_REQUESTED,
        $somePolicy
    );

    $eventDispatcher->attach(
        ProductsController::DELETE_REQUESTED,
        $somePolicy
    );

.. note:: To learn more about the ``\Zend\EventManager\ListenerAggregateInterface`` interface, please see the `ZF documentation on Aggregate Listeners <https://zendframework.github.io/zend-eventmanager/aggregates/>`_.

Thus, the Policy will listen for any of the above mentioned events from within this aggregate.

.. index:: Event Sourcing Commands

.. _eventsourcing command:

Aggregate Commands
------------------

Aggregate commands can be of two types: blocking or non-blocking. When using non-blocking commands, one
should use an instance of the ``\Ascmvc\EventSourcing\CommandRunner`` class to run an
``\Ascmvc\EventSourcing\AsyncCommand`` command, which extends the ``Symfony\Component\Console\Command\Command``
class. To use an asynchronous command, one must give it a name and give the command a body. Here is an
example of an async command:

.. code-block:: php

    use Application\Models\Entity\Products;
    use Application\Models\Repository\ProductsRepository;
    use Ascmvc\AbstractApp;
    use Ascmvc\EventSourcing\AsyncCommand;
    use Doctrine\ORM\Mapping\ClassMetadata;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Output\OutputInterface;

    class ReadProductsCommand extends AsyncCommand
    {
        protected static $defaultName = 'products:read';

        public function __construct(AbstractApp $webapp)
        {
            // you *must* call the parent constructor
            parent::__construct($webapp);
        }

        protected function configure()
        {
            $this
                ->setName('products:read')
                ->setDescription("Query Doctrine for 'Products' entities.");
            $this
                // configure options
                ->addOption('values', null, InputOption::VALUE_REQUIRED, 'Specify a serialized value object array to use.');
        }

        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $connName = $this->getWebapp()->getBaseConfig()['events']['read_conn_name'];

            $entityManager = $this->getWebapp()->getServiceManager()[$connName];

            $serializedAggregateValueObjectProperties = $input->getOption('values');

            if (!empty($serializedAggregateValueObjectProperties)) {
                $args = unserialize($serializedAggregateValueObjectProperties);
            } else {
                $args = [];
            }

            $productsRepository = new ProductsRepository(
                $entityManager,
                new ClassMetadata(Products::class)
            );

            try {
                if (isset($args['id'])) {
                    $result = $productsRepository->find($args['id']);

                    if (!is_null($result)) {
                        $results[] = $productsRepository->hydrateArray($result);
                    } else {
                        $results = [];
                    }
                } else {
                    $results = $productsRepository->findAll();
                }
            } catch (\Exception $e) {
                return 1;
            }

            if (!empty($results)) {
                $outputValues = serialize($results);
            } else {
                $outputValues = '';
            }

            $output->writeln($outputValues);
        }
    }

Once the command is named and defined, one must add it to the ``async_commands`` index of the ``$baseConfig``
array. This way, the command bus (``\Ascmvc\EventSourcing\CommandRunner``) will know how to find the command.
The command will then be executed according to the requirements of the PHP environment within which the command
is called. When in a non-Swoole environment, the command will be forked using
``Symfony\Component\Process\Process`` and **ReactPHP**. Otherwise, the command bus will call the command
from within Swoole's coroutine.

If one only wishes to call a simple blocking command, the ``\Ascmvc\EventSourcing\Command`` offers a very
simple blueprint that defines common functionality to be used by all commands. Command classes should
extend this base class and should represent an imperative that takes place within an aggregate.
If one is to say "write this data about our products to the database", one
should extend the ``\Ascmvc\EventSourcing\Command`` class and name the class ``WriteProductsCommand`` within
the namespace of the aggregate. Once the command has finished executing itself, it should dispatch a new
aggregate event in order to notify listeners that the command is finished. Here is an example of what a
``WriteProductsCommand`` class could look like:

.. code-block:: php

    <?php

    namespace Application\Commands;

    use Application\Controllers\ProductsController;
    use Application\Events\WriteProductsCompleted;
    use Application\Models\Entity\Products;
    use Application\Models\Repository\ProductsRepository;
    use Ascmvc\EventSourcing\AggregateImmutableValueObject;
    use Doctrine\ORM\Mapping\ClassMetadata;

    class WriteProductsCommand extends ProductsCommand
    {
        public function execute()
        {
            $name = $this->argv['name'];

            $args = $this->aggregateValueObject->getProperties();

            $productsRepository = new ProductsRepository(
                $this->entityManager,
                new ClassMetadata(Products::class)
            );

            $values = [];

            try {
                if ($name === ProductsController::CREATE_REQUESTED) {
                    $productsRepository->save($args);
                } elseif ($name === ProductsController::UPDATE_REQUESTED) {
                    $products = $this->entityManager->find(Products::class, $args['id']);

                    $values['pre'] = [
                        'id' => $products->getId(),
                        'name' => $products->getName(),
                        'price' => $products->getPrice(),
                        'description' => $products->getDescription(),
                        'image' => $products->getImage(),
                    ];

                    $productsRepository->save($args, $products);
                } elseif ($name === ProductsController::DELETE_REQUESTED) {
                    if (isset($args['id'])) {
                        $products = $this->entityManager->find(Products::class, $args['id']);
                        $productsRepository->delete($products);
                    }
                }

                $params = ['saved' => 1];

                $values['post'] = $args;

                $aggregateValueObject = new AggregateImmutableValueObject($values);

                if ($name === ProductsController::CREATE_REQUESTED) {
                    $event = new WriteProductsCompleted(
                        $aggregateValueObject,
                        ProductsController::class,
                        ProductsController::CREATE_COMPLETED
                    );
                } elseif ($name === ProductsController::UPDATE_REQUESTED) {
                    $event = new WriteProductsCompleted(
                        $aggregateValueObject,
                        ProductsController::class,
                        ProductsController::UPDATE_COMPLETED
                    );
                } elseif ($name === ProductsController::DELETE_REQUESTED) {
                    $event = new WriteProductsCompleted(
                        $aggregateValueObject,
                        ProductsController::class,
                        ProductsController::DELETE_COMPLETED
                    );
                }

                $event->setParams($params);
            } catch (\Exception $e) {
                $event->setParam('error', 1);
            }

            $this->eventDispatcher->dispatch($event);
        }
    }

This new class will then be ready to be called by a ``\Ascmvc\EventSourcing\Policy`` listener once the
corresponding event will be dispatched by another object, whether it is the main command
(controller action method) or a subordinate command.

.. index:: Event Sourcing Logger

.. _eventsourcing logger:

Event Logger
------------

LightMVC Framework's event sourcing implementation comes with ``\Ascmvc\EventSourcing\EventLogger`` that
will log any event based on two criteria: 1- any aggregate that has added the ``EventLogger`` class
name to its event bus identifiers, and 2- any whitelisted (or not blacklisted) event class type. Concerning
this second criterium, the logger will log all events if no classes were whitelisted or blacklisted. If one
class is whitelisted or blacklisted, the logger will blacklist by default.

Also, it is possible to log events to a different database if a Doctrine ORM connection name is defined
for it in the application's configuration.

.. note:: For more information on configuring an application's event log, please see the :ref:`configuration eventsourcing` section.

For a working example of Event Sourcing and CQRS with LightMVC, please use our skeleton application as it is explained in the section on the :ref:`skeleton`.