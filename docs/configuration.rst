.. _ConfigurationAnchor:

.. index:: Configuration

.. index:: Configuration files

.. _configuration:

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
* ``templateDir`` under the ``templates`` index, which contains the name of the folder where the templates are stored,
* ``events``, which contains an array of parameters in order to configure the LightMVC Event Sourcing controller-based aggregates,
* ``session``, which contains an array of parameters in order to configure the LightMVC asynchronous PHP session.

.. note:: The Twig and Smarty template managers require additional indexes under the ``templates`` index. These are: ``compileDir``, ``configDir`` and ``cacheDir``.

Also, there are three optional preconfigured indexes in the ``$baseConfig`` array:

* ``middleware``, which contains an array of PSR-15 compliant middleware to be used,
* ``eventlog``, which contains an array of parameters in order to configure the LightMVC Event Sourcing Logger,
* ``doctrine``, which contains an array of parameters in order to configure one or more Doctrine connections.
* ``atlas``, which contains an array of parameters in order to configure one or more Atlas connections.

Here is an example of a ``config/config.php`` file:

.. code-block:: php

    <?php

    $baseConfig['env'] = 'production'; // 'development' or 'production'

    $baseConfig['appName'] = 'The LightMVC Framework Skeleton Application';

    // Required configuration
    require 'events.config.php';

    require 'routes.config.php';

    require 'view.config.php';

    // Optional configuration
    include 'middleware.config.php';

    include 'session.config.php';

.. _configuration eventsourcing:

Event Sourcing Configuration
----------------------------

For example, the ``config/events.config.php`` file might look like the following:

.. code-block:: php

    <?php

    $baseConfig['events'] = [
        // PSR-14 compliant Event Bus.
        'psr14_event_dispatcher' => \Ascmvc\EventSourcing\EventDispatcher::class,
        // Different read and write connections allow for simplified (!) CQRS. :)
        'read_conn_name' => 'dem1',
        'write_conn_name' => 'dem1',
    ];

    $baseConfig['eventlog'] = [
        'enabled' => true,
        'doctrine' => [
            'log_conn_name' => 'dem1',
            'entity_name' => \Application\Log\Entity\EventLog::class,
        ],
        // Leave empty to log everything, including the kitchen sink. :)
        // If you you start whitelisting events, it will blacklist everything else by default.
        'log_event_type' => [
            'whitelist' => [
                \Ascmvc\EventSourcing\Event\WriteAggregateCompletedEvent::class,
            ],
            'blacklist' => [
                //\Ascmvc\EventSourcing\Event\AggregateEvent::class,
            ],
        ],
    ];

.. note:: For more information on configuring the application's event sourcing aggregates and the application's event log, please see the :ref:`eventsourcing` section.

.. _configuration routing:

Routing Configuration
---------------------

The ``config/routes.config.php`` file might look like the following:

.. code-block:: php

    $baseConfig['routes'] = [
        0 => [
            'GET',
            '/',
            'index',
        ],
    ];

.. note:: For more information on configuring the application's routes, please see the :ref:`routing` section.

.. _configuration session:

Session Configuration
---------------------

And, the ``config/session.config.php`` file might look like this:

.. code-block:: php

    $baseConfig['session'] = [
        'enabled' => true,
        'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
        'doctrine_cache_driver' => \Doctrine\Common\Cache\FilesystemCache::class,
        //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
        //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
        //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
        //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
        'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
        'doctrine_cache_server_params' => [
            'host' => '127.0.0.1',
            'port' => 6379, // redis
            //'port' => 11211 // memcached/memcache
        ],
        'session_name' => 'PHPSESSION',
        'session_path' => '/',
        'session_id_length' => 32,
        'session_id_type' => 1,
        'session_storage_prefix' => 'ascmvc',
        'session_expire' => 60 * 30, // 30 minutes
    ];

It is possible to replace the value of the ``psr6_cache_pool`` index with any PSR-6 compliant class. Also,
any of the given Doctrine\\Common\\Cache classes can be used in order to store the session data when using
the ``\Ascmvc\Session\Cache\DoctrineCacheItemPool`` class.

For more information on sessions, please see the :ref:`sessions` section.

.. _configuration view:

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
                $baseConfig['URLBASEADDR'] . 'js/jquery-3.3.1.min.js',
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

For more information on configuring the application's view, please see the :ref:`views` section.

.. index:: Model configuration

.. index:: Configuration Model

.. _configuration middleware:

Middleware Configuration
------------------------

The ``config/middleware.config.php`` file might look like the following:

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

.. note:: The :ref:`middleware` section contains all the needed information in order to set up PSR-15 compliant middleware.

.. index:: View configuration

.. index:: Configuration View

.. _configuration model:

Model Configuration
-------------------

Finally, you can configure Doctrine within a ``config/config.local.php`` file, as follows:

.. code-block:: php

    $baseConfig['doctrine']['DBAL']['dcm1'] = [
        'driver'   => 'pdo_mysql',
        'host'     => 'localhost',
        'user'     => 'USERNAME',
        'password' => 'PASSWORD',
        'dbname'   => 'DATABASE',
    ];

    // AND/OR

    $baseConfig['doctrine']['ORM']['dem1'] = [
        'driver'   => 'pdo_mysql',
        'host'     => 'localhost',
        'user'     => 'USERNAME',
        'password' => 'PASSWORD',
        'dbname'   => 'DATABASE',
    ];

    $baseConfig['atlas']['ORM']['aem1'] = [
        'driver'   => 'mysql',
        'host'     => 'localhost',
        'user'     => 'USERNAME',
        'password' => 'PASSWORD',
        'dbname'   => 'DATABASE',
    ];

Then, it would be possible to get the connection to the database by asking the Service Manager for it,
from within a controller factory for example, in this way:

.. code-block:: php

    $dcm1 = $serviceManager['dcm1'];

    // AND/OR

    $dem1 = $serviceManager['dem1'];

    // AND/OR

    $aem1 = $serviceManager['aem1'];

.. note:: Atlas and Doctrine DBAL and ORM objects are lazy-loaded, which avoids creating instances of these classes if they remain unused.

For more information on configuring the application's model, please see the :ref:`models` section.
