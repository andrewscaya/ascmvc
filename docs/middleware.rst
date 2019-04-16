.. _MiddlewareAnchor:

.. index:: Middleware

.. _middleware:

Middleware
==========

The LightMVC Framework uses **Zend Stratigility** for its middleware implementation. This implementation is therefore PSR-15 compliant.

Configuring middleware is very straightforward in the LightMVC Framework. In a ``config/middleware.config.php`` file, one might
configure some middleware as per the following:

.. code-block:: php

    $baseConfig['middleware'] = [
        '/foo' => function ($req, $handler) {
            $response = new \Zend\Diactoros\Response();
            $response->getBody()->write('FOO!');

            return $response;
        },
        function ($req, $handler) {
            if (! in_array($req->getUri()->getPath(), ['/bar'], true)) {
                return $handler->handle($req);
            }

            $response = new \Zend\Diactoros\Response();
            $response->getBody()->write('Hello world!');

            return $response;
        },
        '/baz' => [
            \Application\Middleware\SessionMiddleware::class,
            \Application\Middleware\ExampleMiddleware::class,
        ],
    ];

Any callable or any class that implements the ``\Psr\Http\Server\MiddlewareInterface`` interface can be
used as valid middleware.

.. code-block:: php

    use \Psr\Http\Server\MiddlewareInterface;

    class ExampleMiddleware implements MiddlewareInterface
    {
        public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
        {
        }
    }

Middleware can be configured with or without paths. When indicating a path as the name of the array index of
the middleware, the middleware will only run if the path matches the request URI. If the middleware's array index
is an integer, the middleware will run on every request. Finally, it is possible to stack middleware within
the same array index. In this latter case, the LightMVC Framework will lazy-load this FIFO stack of middleware
and will run it in the given order.

.. note:: Normally, the middleware pipeline should always return a valid PSR-7 compliant response object. Otherwise, the pipeline would throw an exception. In the case of the LightMVC Framework, the pipeline can quietly fail in order to allow the MVC components to handle the request.

By default, the middleware pipeline is attached as a listener to the ``AscmvcEvent::EVENT_BOOTSTRAP`` event and can be overridden by a high priority listener on this event.

For more information on **Zend Stratigility**, please see
the `Zend Stratigility website <https://docs.zendframework.com/zend-stratigility/>`_.

You can also see the :ref:`configuration middleware` section.