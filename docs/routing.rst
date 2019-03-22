.. _RoutingAnchor:

.. index:: Routing

.. index:: Router

.. index:: FastRouter

.. index:: FastRoute

.. _routing:

Routing
=======

The framework uses the nikic/fast-route library - ``FastRoute`` - as its main routing service.

All configuration must be given in the ``$baseConfig`` array, under the ``routes`` index.

.. note:: For more information on configuring the application's routes, please see the :ref:`configuration` section.

.. index:: Routes

.. _routes:

Routes
------

Defining a route is as simple as adding an integer-referenced array to the ``routes`` array contained
within the ``$baseConfig`` array. This new integer-referenced array must contain three elements,
in the following order: 1- an HTTP verb, 2- a URL, and 3- the name of the controller.

1. The HTTP verb can be one of ``GET``, ``POST``, ``PUT``, ``PATCH``, or ``DELETE``. An array of multiple
HTTP verbs can be given.

.. note:: If a page is requested with another HTTP verb than the ones that are defined in the corresponding route, the application will return a '405 - Method Not Allowed' header.

2. The URL can contain named placeholders. These are defined by using curly braces. By default, any placeholder
named ``action`` will be mapped to the name of a controller's handler method. The default handler method of
any controller is the ``indexAction`` action. By default, **LightMVC** is a 'single-action controller'
framework.

The contents of any placeholder will be available within the $vars variable inside the
controller's handler method, under an index with the name of the corresponding HTTP verb, and a sub-index
with the name of the placeholder. Finally, any part of the URL can be defined as being optional by using
square brackets.

.. note:: It is possible to use a regex in order to only allow some characters in the URL.

3. The name of the controller must be in lowercase and must map to an existing controller in order to avoid
a runtime exception that will be thrown by the controller manager when it tries to get the requested handler
(controller method).

.. note:: The controller name can contain a forward slash ('/') in order to reference to a module name. For example, ``special/index`` would map to the ``IndexController`` within the ``Special\Controller\`` namespace. When no forward slash is given, the default namespace is ``Application\Controller\``.

Here is an example of a more advanced route configuration:

.. code-block:: php

    $baseConfig['routes'] = [
        2 => [
            ['GET', 'POST'],
            '/products[/{action}[/{id:[0-9]+}]]',
            'product',
        ],
    ];

In this example, only ``GET`` and ``POST`` requests will be allowed on any URL beginning with ``/products``.
The URL can also contain an ``action`` name, which will map to a request handler method within the controller,
and an ``id`` parameter, which will contain at least one digit, ranging from 0 through 9. The contents of the
``action`` and ``id`` placeholders will be available in the $vars variable within the controller's request
handler method: ``$vars['get']['action']`` and ``$vars['get']['id']``.

For further reading on the ``FastRoute`` object, please see the
`FastRoute Code Repository <https://github.com/nikic/FastRoute>`_.

.. index:: Caching routes

.. _caching routes:

Caching Routes
--------------

When running a **LightMVC** application in ``production`` mode (please see the :ref:`configuration` section
for more details), routes will be cached. It is therefore important to delete the ``cache/routes.cache``
file in order to refresh the cache if need be.