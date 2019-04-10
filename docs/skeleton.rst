.. _SkeletonAnchor:

.. index:: Skeleton Application

.. _skeleton:

LightMVC Skeleton Application
=============================

A **LightMVC Framework Skeleton Application** is available as a working example of the framework itself.

.. index:: Skeleton Application Installation

.. _skeleton installation:

Installation
------------

To start using the **Skeleton Application**, simply clone it from Github::

    $ git clone https://github.com/lightmvc/lightmvcskel


.. note:: Please make sure that the ``public`` folder is accessible to the Web server and that the ``cache``, ``logs`` and ``templates_c`` folders are writable.

Once cloned, enter the directory and install the dependencies using **Composer**::

    $ cd lightmvcskel
    $ composer install

In order to use all the included features, one must create and populate the database. A sample database
is included in the ``data`` folder.

Furthermore, a sample virtual host configuration file for the **Apache** Web server is included in this same folder.

.. index:: Skeleton Application Structure

.. _skeleton structure:

File Structure
--------------

The application's file structure is quite straightforward:

* ``cache``: contains files created by the application while running in ``production`` mode.
* ``config``: contains all of the application's configuration files.
* ``controllers``: contains all of the application's controllers, grouped by module (namespace).
* ``data``: contains any data file (database backups, configuration files for external services, etc.).
* ``lib``: contains any libraries that are not available through Composer.
* ``logs``: contains all of the application's log files.
* ``middleware``: contains all of the application's middleware classes.
* ``models``: contains all of the application's model classes (entities, repositories, backend traits, etc.).
* ``public``: contains the front controller file, the Swoole front controller file, and all other static files (CSS, JS, images, fonts, favicons, etc.).
* ``templates``: contains all of the application's template files, grouped by template manager.
* ``templates_c``: contains all of the application's compiled template files when running in ``production`` mode (except when using ``Plates``).
* ``vendor``: contains all of the application's installed dependencies through **Composer**.

.. index:: Swoole

.. _swoole:

Running on Swoole
-----------------

The **LightMVC Framework Skeleton Application** can run on Swoole in order to make it lightning fast. In order
to do so, you must make sure to install Swoole. From the CLI, as the root user, type the following::

    $ pecl install swoole

After answering a few questions, Swoole will be compiled and installed. Then, as the root user, run the following command (on Linux/Unix/Mac)::

    $ echo "extension=swoole.so" >> /etc/php.ini

.. note:: If running **Swoole** on **Windows**, please add the extension manually in **PHP**'s ``php.ini`` file. The ``php.ini`` file might be located elsewhere on your system. For example, on **Ubuntu** 18.04, when running **PHP** 7.2, you will find this file in ``/etc/php/7.2/apache2``. You can discover the location of this file by entering the command ``php --ini`` on the command line. It must also be mentioned that some systems have multiple INI files (CLI vs Web). Please modify all those that apply.

Then, from within the root directory of the project, you can run the following command::

    $ COMPOSER_PROCESS_TIMEOUT=0 composer run-swoole

.. note:: By default, Swoole will listen on the ``localhost`` loopback, on port 9501. If you wish to change this, please modify the ``run-swoole`` command inside the ``composer.json`` file accordingly.

Have a lot of fun! :)
