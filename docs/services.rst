.. _ServicesAnchor:

.. index:: Services

.. _services:

Services
========

This framework's main services are:

* Event Manager (``\Ascmvc\Mvc\AscmvcEventManager``),
* Service Manager (``\Pimple\Container``).

.. index:: Event Manager

.. _event manager:

Event Manager
-------------

The ``\Ascmvc\Mvc\AscmvcEventManager`` event manager is an extension of the ``\Laminas\EventManager\EventManager``.
It is available through the application object's ``getEventManager()`` method. It is configured **WITH** a
``\Laminas\EventManager\SharedEventManager``. It is possible to get the shared manager by calling the main
event manager's ``getSharedManager()`` method. This same shared manager will also be readily available
within each controller aggregate by getting it from the controller's PSR-14 event dispatcher (event bus)
like so:

.. code-block:: php

    // From within a controller's action method for example.
    $sharedEventManager = $this->eventDispatcher->getSharedManager();

By doing so, it becomes possible to dispatch custom events not only to other parts of the current aggregate,
but to also dispatch custom events to other aggregates outside of the current controller aggregate. Thus,
Aspect-Oriented Programming becomes a clear possibility and allows for separation of concerns and
code modularity.

.. note:: Each controller has access to a segregated event dispatcher (event bus), as the controller is considered to be the Aggregate Root of its event sourcing aggregate.

For more information on configuring the controller's event dispatcher, please see the :ref:`configuration eventsourcing` section.

The main ``AscmvcEventManager`` is designed to be able to trigger ``\Ascmvc\Mvc\AscmvcEvent`` events for the
entire application. The ``\Ascmvc\Mvc\AscmvcEvent`` class is an extension of the ``Laminas\EventManager\Event``
class. Here is a list of the framework's main MVC events:

.. code-block:: php

    /**#@+
     * Mvc events triggered by the Event Manager
     */
    const EVENT_BOOTSTRAP      = 'bootstrap';
    const EVENT_ROUTE          = 'route';
    const EVENT_DISPATCH       = 'dispatch';
    const EVENT_RENDER         = 'render';
    const EVENT_FINISH         = 'finish';
    /**#@-*/

These events correspond to listener interfaces that are implemented by default in each and every controller.
Thus, from within any controller, it is possible to tap into a specific MVC event, or to downright interrupt
the application's flow by returning a ``\Laminas\Diactoros\Response``, from within these listener methods.

Here is a short description of each main event:

    * EVENT_BOOTSTRAP (onBootstrap): this event is triggered right after the booting and initialization phases of the application. Using the onBootstrap method within a controller class makes it possible to run code immediately after the middleware pipeline. And, if you attach a listener to this event with a high priority, you can run code before the execution of any middleware, any controller or any service.
    * EVENT_ROUTE (onRoute): this event is triggered after bootstrapping is done and the router class has been instantiated, but before the router actually tries to resolve the request URI to a handler.
    * EVENT_DISPATCH (onDispatch): this event is triggered after the router has instantiated a controller manager with a requested handler, but before the controller manager actually hands control over to the requested controller.
    * EVENT_RENDER (onRender): this event is triggered once the controller has returned its output, but before the output is parsed by the template managers.
    * EVENT_FINISH (onFinish): this event is triggered once rendering is done and/or a response object is available (event short-circuit). This event allows to manipulate the response object before returning the response to the client.

.. note:: You should avoid as much as possible to use the onBootstrap() method within the controller classes, as this would not scale very well if there is a large number of controllers.

.. note:: If you run the framework using **Swoole**, you should avoid using a high priority ``AscmvcEvent::EVENT_FINISH`` listeners to manipulate the response, because this event will be ignored by Swoole. To achieve the same result, one should use a very low priority listener on the ``AscmvcEvent::EVENT_RENDER`` event instead.

Here is an example of a controller that is tapping into the ``AscmvcEvent::EVENT_BOOTSTRAP`` event in order to short-circuit the
application's execution and return an early response:

.. code-block:: php

    <?php

    namespace Application\Controllers;

    use Ascmvc\Mvc\AscmvcEvent;
    use Ascmvc\Mvc\Controller;
    use Laminas\Diactoros\Response;

    class FakeController extends Controller
    {
        public static function onBootstrap(AscmvcEvent $event)
        {
            $response = new Response();
            $response->getBody()->write('Hello World!');
            return $response;
        }

    // [...]

In order to attach a new listener to one of the main MVC events, you can simply do it this way:

.. code-block:: php

    $this->event->getApplication()->getEventManager()->attach(AscmvcEvent::EVENT_RENDER, function ($event) use ($serviceManager) {
        // do something here
    }, 3);

.. note:: The last parameter is a priority indicator. The higher the indicator, the higher the priority of the listener. Any listener can be given a priority of three (3) or more in order to run **BEFORE** any of the preconfigured listeners.

To learn more about the LightMVC events and and corresponding listeners, please see the **LightMVC Framework**'s
`API documentation <http://apidocs.lightmvcframework.net/namespaces/Ascmvc.html>`_.

For more information on available methods of the ``\Laminas\EventManager\EventManager``, please see
the `Laminas documentation <https://docs.laminas.dev/laminas-eventmanager/>`_,
and the `Laminas API documentation <https://docs.laminas.dev/laminas-eventmanager/api/>`_.

.. index:: Service Manager

.. _service manager:

Service Manager
---------------

The LightMVC Service Manager is an instance of the ``\Pimple\Container`` class. It is a simple implementation
of a Registry and allows for easy storage and retrieval of objects and data. The Pimple container object
implements the ``\ArrayAccess`` interface and thus, can be accessed as if it was an array.

Storing a service is as simple as:

.. code-block:: php

    // Store SomeService instance
    $serviceManager['someService'] = function ($serviceManager) {
        return new SomeService();
    };

And, retrieving the same service would be done as follows:

.. code-block:: php

    // Retrieve SomeService instance
    $someService = $serviceManager['someService'];

It is possible to store a service within the container as a lazy-loading one. To do so, you must use the
container's ``factory()`` method:

.. code-block:: php

    // Store SomeService instance
    $serviceManager['someService'] = $serviceManager->factory(function ($serviceManager) {
        // Retrieve the database connection and inject it within the SomeService constructor
        return new SomeService($serviceManager['dem1]);
    });

To learn more about **Pimple**, please visit the `Pimple Website <https://pimple.symfony.com/>`_.
