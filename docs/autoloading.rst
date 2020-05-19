.. _AutoloadingAnchor:

.. index:: Autoloading

.. _autoloading:

Autoloading
===========

The framework's autoloading is managed by **Composer**. By default, the LightMVC Framework uses PSR-4 compliant
autoloading. To add new namespaces within a LightMVC application, it is necessary to declare these namespace
mappings within the application's ``composer.json`` file. For example, here are the namespaces of the
**LightMVC Skeleton Application**::

    "autoload": {
      "psr-4": {
        "Application\\Models\\": "models/Application/Models",
        "Application\\Middleware\\": "middleware/Application/Middleware",
        "Application\\Controllers\\": "controllers/Application/Controllers",
        "Application\\Services\\": "controllers/Application/Services",
        "Specialmodule\\Controllers\\": "controllers/Specialmodule/Controllers"
      }
    },

Therefore, adding new namespaces is simply a question of adding new entries in this part of the file and running
the following command from a CLI::

    $ composer update

.. note:: A PSR-4 autoloader class is available within the framework if you wish to use configuration files instead of Composer's autoloading capabilities.