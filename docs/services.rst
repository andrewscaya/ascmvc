.. index:: Services

.. _Services:

Services
========

The framework's main services are:

* Event Manager (``\Ascmvc\Mvc\AscmvcEventManager``),
* Service Manager (``\Pimple\Container``).

.. index:: Event Manager

.. _Event manager:

Event Manager
-------------

The ``\Ascmvc\Mvc\AscmvcEventManager`` event manager is an extension of the ``\Zend\EventManager\EventManager``.
It is available through the application object's ``getEventManager()`` method. It is configured **WITHOUT** a
``\Zend\EventManager\SharedEventManager``.

The ``AscmvcEventManager`` is designed to be able to trigger ``\Ascmvc\Mvc\AscmvcEvent`` events. The ``\Ascmvc\Mvc\AscmvcEvent``
class is an extension of the ``Zend\EventManager\Event`` class. Here is a list of the framework's main MVC events:

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

These events correspond to listener interfaces that are implemented by default in every controller. Thus,
from within any controller, it is possible to tap into a specific MVC event, or to downright interrupt
the application's flow by returning a ``\Zend\Diactoros\Response``, from within these listener methods.

Here is an example of a controller that is tapping into the ``onBootstrap`` event in order to short-circuit the
application's execution and return an early response:

.. code-block:: php

    <?php

    namespace Application\Controllers;

    use Ascmvc\Mvc\AscmvcEvent;
    use Ascmvc\Mvc\Controller;
    use Zend\Diactoros\Response;

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

    $this->eventManager->attach(AscmvcEvent::EVENT_BOOTSTRAP, function ($event) use ($serviceManager) {
        // do something here
    }, 3);

.. note:: The last parameter is a priority indicator. The higher the indicator, the higher the priority of the listener. Any listener can be given a priority of three (3) or more in order to run **BEFORE** any of the preconfigured listeners.

To learn more about the LightMVC events and and corresponding listeners, please read the LightMVC Framework's
`API documentation <http://apidocs.lightmvcframework.net/namespaces/Ascmvc.html>`_.

For more information on available methods of the ``\Zend\EventManager\EventManager``, please see
the `ZF documentation <https://framework.zend.com/manual/2.4/en/modules/zend.event-manager.event-manager.html>`_,
and the `ZF API documentation <https://framework.zend.com/apidoc/2.4/index.html>`_.

.. index:: Service Manager

.. _Service manager:

Service Manager
---------------

The LightMVC Service Manager is an instance of the ``\Pimple\Container`` class. It is a simple implementation
of a Registry and allows for easy storage and retrieval of objects and data. The Pimple container object
implements the ``\ArrayAccess`` interface and thus, can be accessed as if it was an array.

Storing a service is as easy as:

.. code-block:: php

    // Store SomeService instance
    $serviceManager['someService'] = function ($serviceManager) {
        return new SomeService();
    };

And, retrieving the same service is just as easy:

.. code-block:: php

    // Retrieve SomeService instance
    $someService = $serviceManager['someService'];

It is possible to store a service within the container as a lazy-loading one. To do so, you must use the
container's ``factory()`` method:

.. code-block:: php

    // Store SomeService instance
    $serviceManager['someService'] = $serviceManager->factory(function ($serviceManager) {
        // Retrieve the database connection and inject it within the SomeService constructor
        return new SomeService($serviceManager['em1]);
    });

To learn more about Pimple, please visit the `Pimple Website <https://pimple.symfony.com/>`_.
