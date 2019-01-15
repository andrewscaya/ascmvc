.. _ControllersAnchor:

.. index:: Controllers

.. _controllers:

Controllers
===========

The framework's controllers are extensions of the ``Ascmvc\Mvc\Controller`` class which implements
the ``Ascmvc\AscmvcEventManagerListenerInterface`` interface.

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
a minimal version of the application's ``$baseConfig`` array will be injected into the controller. Upon execution
of the controller's request handler method, all the global server variables are injected into the handler
through the ``$vars`` variable.

.. note:: One should avoid as much as possible to use the onBootstrap() method within the controller classes, as this would not scale very well if there is a large number of controllers.

For more information on the event manager and the main MVC events, please see the :ref:`event manager` section.

.. index:: Controller factories

.. index:: Controller factory interface

.. _controller factories:

Controller Factories
--------------------

Any controller can implement the ``Ascmvc\AscmvcControllerFactoryInterface`` interface and become a
factory that will return an instance of itself to the controller manager, after completing some specific logic.
This is useful if you need to set up some specific service or resource before injecting it into an instance
of the controller.

.. note:: It is not recommended to inject the entire application object or the service manager into the controller, but to only inject the services that you actually need in order to respond to the request.

For a working example, please see the section on the :ref:`skeleton`.

For information on how to deal with other types of factories, please see the :ref:`service manager` section.