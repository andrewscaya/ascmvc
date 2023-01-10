# [![LightMVC Banner](https://github.com/lightmvc/lightmvcskel/raw/master/public/img/lightmvc_logo.png)](https://lightmvcframework.net/)
# LightMVC Framework

https://lightmvcframework.net

Easily create PHP applications by using any PHP library within this very modular, event-driven and Swoole-enabled framework!

[![Build Status](https://travis-ci.org/lightmvc/ascmvc.svg?branch=master)](https://travis-ci.org/lightmvc/ascmvc)
[![Coverage Status](docs/images/code_coverage.svg)](https://lightmvcframework.net/)
[![Documentation Status](https://readthedocs.org/projects/lightmvc-framework/badge/?version=latest)](https://lightmvc-framework.readthedocs.io/en/latest/?badge=latest)
[![PDS Skeleton](https://img.shields.io/badge/pds-skeleton-blue.svg?style=flat-square)](https://github.com/php-pds/skeleton)

To add the **LightMVC Framework** package to your PHP project, you can simply run the following command on your computer's CLI:

    $ composer require lightmvc/ascmvc

You can also use the **LightMVC Skeleton Application** by issuing these commands:

    $ git clone https://github.com/lightmvc/lightmvcskel
    $ cd lightmvcskel
    $ composer install

> The LightMVC Skeleton Application can also be downloaded as an archive file from https://lightmvcframework.net/download.

The **LightMVC Framework Skeleton Application** can run on Swoole in order to make it lightning fast. In order
to do so, you must make sure to install Swoole. From the CLI, as the root user, type the following:

    $ pecl install swoole

After answering a few questions, Swoole will be compiled and installed. Then, as the root user, run the following command (on Linux/Unix/Mac):

    $ echo "extension=swoole.so" >> /etc/php.ini

> If running **Swoole** on **Windows**, please add the extension manually in **PHP**'s ``php.ini`` file. The ``php.ini`` file might be located elsewhere on your system. For example, on **Ubuntu** 18.04, when running **PHP** 7.2, you will find this file in ``/etc/php/7.2/apache2``. You can discover the location of this file by entering the command ``php --ini`` on the command line. It must also be mentioned that some systems have multiple INI files (CLI vs Web). Please modify all those that apply.

Then, from within the root directory of the project, you can run the following command:

    $ COMPOSER_PROCESS_TIMEOUT=0 composer run-swoole

> By default, Swoole will listen on the ``localhost`` loopback, on port 9501. If you wish to change this, please modify the ``run-swoole`` command inside the ``composer.json`` file accordingly.

## Upgrading from 4.0.0 to 5.0.0

When upgrading from version 4.0.0 to 5.0.0, please make sure to replace the \Doctrine\Common namespace with the \Ascmvc\Session\Common in your config/session.config.php file.

### Have a lot of fun! :)