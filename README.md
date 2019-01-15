# [![LightMVC Banner](docs/images/logo.png)](https://lightmvcframework.net/)
# LightMVC Framework

https://lightmvcframework.net

Easily create PHP applications by using any PHP library within this very modular, event-driven and Swoole-enabled framework!

[![Build Status](https://travis-ci.org/lightmvc/ascmvc.svg?branch=master)](https://travis-ci.org/lightmvc/ascmvc)
[![Coverage Status](docs/images/code_coverage.svg)](https://lightmvcframework.net/)
[![Documentation Status](https://readthedocs.org/projects/lightmvc/badge/?version=latest)](https://lightmvc.readthedocs.io/en/latest/?badge=latest)
[![PDS Skeleton](https://img.shields.io/badge/pds-skeleton-blue.svg?style=flat-square)](https://github.com/php-pds/skeleton)

To add the **LightMVC Framework** package to your PHP project, you can simply run the following command on your computer's CLI:

    $ composer require lightmvc/ascmvc

You can also use the **LightMVC Skeleton Application** by issuing these commands:

    $ git clone https://github.com/lightmvc/lightmvcskel
    $ cd lightmvcskel
    $ composer install

> The LightMVC Skeleton Application can also be downloaded as an archive file from the https://lightmvc.net/download.

The **LightMVC Framework Skeleton Application** can run on Swoole in order to make it lightning fast. In order
to do so, you must make sure to install Swoole. From the CLI, type the following:

    $ pecl install swoole

After answering a few questions, Swoole will be compiled and installed. Then, run the following command (on Linux/Unix/Mac):

    $ echo "extension=swoole.so" >> /etc/php.ini

> If running Swoole on Windows, please add the extension manually in **PHP**'s php.ini file.

Then, from within the root directory of the project, you can run the following command:

    $ composer run-swoole

> By default, Swoole will listen on the ``localhost`` loopback, on port 9501. If you wish to change this, please modify the ``run-swoole`` command inside the ``composer.json`` file accordingly.

Have a lot of fun! :)
