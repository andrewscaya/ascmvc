.. index:: Configuration

.. index:: Configuration Files

.. _Configuration:

Configuration
=============

The framework's configuration is set within a configuration file named ``config/config.php`` inside
the project's root directory. It is possible to override the configurations found within this file
by creating a file named ``config.local.php`` within the same ``config`` directory.

All configuration options must be stored within an index of the ``$baseConfig`` array.

The main preconfigured indexes of this array are:

* ``BASEDIR``, which contains the full path to the project's root directory,
* ``URLBASEADDR``, which contains the Web URL of the project,
* ``appFolder``, which contains the name of the project's root directory,
* ``env``, which contains an environment setting ('production' or 'development'),
* ``routes``, which contains an array of FastRouter routes to be used,
* ``templateManager``, which contains the name of the Template Manager that is to be used ('Plates', 'Twig' or 'Smarty'),
* ``templateDir`` under the ``templates`` index, which contains the name of the folder where the templates are stored.

.. note:: The Twig and Smarty template managers require additional indexes under the ``templates`` index. These are: ``compileDir``, ``configDir`` and ``cacheDir``.

Also, there are two optional preconfigured indexes in the ``$baseConfig`` array:

* ``middleware``, which contains an array of PSR-15 compliant middleware to be used,
* ``doctrine``, which contains an array of parameters in order to configure one or more Doctrine connections.


Here is an example of a ``config/config.php`` file:

.. code-block:: php

    <?php

    $baseConfig['env'] = 'production'; // 'development' or 'production'

    $baseConfig['appName'] = 'The LightMVC Framework Skeleton Application';

    require_once 'routes.config.php';

    require_once 'middleware.config.php';

Where the ``config/routes.config.php`` file might look like the following:

.. code-block:: php

    $baseConfig['routes'] = [
        0 => [
            'GET',
            '/',
            'index',
        ],
    ];

.. note:: For more information on configuring the application's routes, please see the :ref:`Routing` section.

And, the ``config/middleware.config.php`` file might look like the following:

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

.. note:: The :ref:`Middleware` section contains all the needed information in order to set up PSR-15 compliant middleware.

.. index:: View configuration

.. index:: Configuration View

.. _Configuration view:

View Configuration
------------------

Moreover, it might be useful to configure common view elements in the local configuration files in
order to keep things simple and use these elements within the application's templates, as these configuration
elements will be available to any of the application's controllers.

Here is an example of setting up common view elements within a ``config/view.config.php`` file:

.. code-block:: php

    $baseConfig['templateManager'] = 'Plates';

    $baseConfig['templates'] = [
        'templateDir' => $baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'plates' . DIRECTORY_SEPARATOR,
        'compileDir' => $baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'templates_c' . DIRECTORY_SEPARATOR,
        'configDir' => $baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR,
        'cacheDir' => $baseConfig['BASEDIR'] . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
    ];

    $baseConfig['view'] = [
        'urlbaseaddr' => $baseConfig['URLBASEADDR'],
        'logo' => $baseConfig['URLBASEADDR'] . 'img/logo.png',
        'favicon' => $baseConfig['URLBASEADDR'] . 'favicon.ico',
        'appname' => $baseConfig['appName'],
        'title' => "Skeleton Application",
        'author' => 'Andrew Caya',
        'description' => 'Small CRUD application',
        'css' =>
            [
                $baseConfig['URLBASEADDR'] . 'css/bootstrap.min.css',
                $baseConfig['URLBASEADDR'] . 'css/dashboard.css',
                $baseConfig['URLBASEADDR'] . 'css/bootstrap.custom.css',
                $baseConfig['URLBASEADDR'] . 'css/dashboard.css',

            ],
        'js' =>
            [
                $baseConfig['URLBASEADDR'] . 'js/jquery.min.js',
                $baseConfig['URLBASEADDR'] . 'js/bootstrap.min.js',

            ],
        'bodyjs' => 0,
        'links' =>
            [
                'Home' => $baseConfig['URLBASEADDR'] . 'index',

            ],
        'navmenu' =>
            [
                'Home' => $baseConfig['URLBASEADDR'] . 'index',

            ],

    ];

For more information on configuring the application's view, please see the :ref:`Views` section.

.. index:: Model configuration

.. index:: Configuration Model

.. _Configuration model:

Model Configuration
-------------------

Finally, you can configure Doctrine within a ``config/config.local.php`` file, as follows:

.. code-block:: php

    $baseConfig['doctrine']['DBAL']['dbm1'] = [
        'driver'   => 'pdo_mysql',
        'host'     => 'localhost',
        'user'     => 'USERNAME',
        'password' => 'PASSWORD',
        'dbname'   => 'DATABASE',
    ];

    // AND/OR

    $baseConfig['doctrine']['ORM']['em1'] = [
        'driver'   => 'pdo_mysql',
        'host'     => 'localhost',
        'user'     => 'USERNAME',
        'password' => 'PASSWORD',
        'dbname'   => 'DATABASE',
    ];

Then, it would be possible to get the connection to the database by asking the Service Manager for it,
from within a controller factory for example, in this way:

.. code-block:: php

    $connection = $serviceManager['dbm1'];

    // AND/OR

    $em = $serviceManager['em1'];

.. note:: The Doctrine DBAL and ORM objects are lazy-loaded, which avoids creating instances of these classes if they are unused.

For more information on configuring the application's model, please see the :ref:`Models` section.
