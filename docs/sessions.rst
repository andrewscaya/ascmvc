.. _SessionsAnchor:

.. index:: Sessions

.. _sessions:

Sessions
========

As mentioned in the :ref:`configuration session` section, setting up asynchronous PHP sessions with LightMVC is
a matter of adding the appropriate configuration in the ``config/session.config.php`` file. But, one might
need to use an asynchronous session in a customized way. To do so, it is necessary to instantiate a session
``Config`` object, constructor injecting into it any custom configuration array that might be deemed
necessary, and obtaining an instance of the ``SessionManager`` object by requesting it through
the ``getSessionManager()`` static method. Once this is done, it is a question of invoking
the ``SessionManager``'s ``start()`` method to get the session started. Here is a working example:

.. code-block:: php

    <?php

    $config['session'] = [
        'enabled' => true,
        'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
        'doctrine_cache_driver' => \Doctrine\Common\Cache\FilesystemCache::class,
        'doctrine_filesystem_cache_directory' => __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
        'session_name' => 'MYSESSION',
        'session_path' => '/',
        'session_id_length' => 32,
        'session_id_type' => 1,
        'session_storage_prefix' => 'myprefix',
        'session_expire' => 60 * 60, // 60 minutes
    ];

    $config = new \Ascmvc\Session\Config($config['session']);
    $sessionManager = \Ascmvc\Session\SessionManager::getSessionManager(null, null, $config);

    try {
        $sessionManager->start();
    } catch (\Throwable $e){
        echo $e->getMessage();
    }

    // Do something here! :)

    // Manually persist the session data.
    // This step is optional, as the session object will persist itself automatically
    // before the PHP script exits and falls out of scope of the PHP runtime.
    $sessionManager->persist();
