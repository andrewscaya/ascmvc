.. _WhatsNewAnchor:

What's new in version 2.0.3 (2019-04-10)
========================================

* Fixes an issue when requesting an unknown controller method.

What's new in version 2.0.2 (2019-01-23)
========================================

* Fixes an issue when running in Swoole mode behind an NGINX HTTPS proxy server.

* Fixes an issue with the way the controller namespace was obtained from the file path.

* Updates the documentation.

What's new in version 2.0.1 (2019-01-20)
========================================

* Fixes an issue with the Bootstrap event on Windows.

* Modifies the Controller Manager in order to receive an instance from a Controller Factory directly.

* Updates the documentation.

* Updates the API documentation.

What's new in version 2.0.0 (2019-01-15)
========================================

* Adds support for running the framework on Swoole.

* Uses PSR-7 compliant HTTP messages (Zend Diactoros).

* Uses PSR-15 compliant middleware and pipelines (Zend Stratigility).

* Uses the nikic/fast-route routing library.

* Uses the Pimple Container as a service manager.

* Uses an event-driven architecture (Zend EventManager).

* Uses Plates as the default template manager.

* Adds Twig as a possible template manager.

* Updates the Smarty template manager.

* Adds TailwindCSS to the default templates.

* Updates Bootstrap CSS Framework in alternative templates.

* Updates the Doctrine Framework (domain logic).

* Adds the Atlas ORM Framework (persistence logic).
