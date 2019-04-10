.. _ViewsAnchor:

.. index:: Views

.. _views:

Views
=====

By default, the framework uses the **Plates** template manager. **Twig** and **Smarty** are also available. In order
to change the template manager, one only has to change the parameters in the ``config/view.config.php`` file.

For more information on configuring the template managers and view elements, please see the :ref:`configuration view` section.

By default, a LightMVC application should hold two folders for the view scripts:
a ``templates`` folder and a ``templates_c`` folder.

The ``templates_c`` folder is used by the **Twig** and **Smarty** template managers in order to store compiled versions
of the templates. This template cache will only be active if the application is in ``production`` mode
(see the :ref:`configuration`). **Plates** does not use a cache by default.

For more information on **Plates**, please see the `Plates website <http://platesphp.com/>`_.

For more information on **Twig**, please see the `Twig website <https://twig.symfony.com/>`_.

For more information on **Smarty**, please see the `Smarty website <https://www.smarty.net/>`_.