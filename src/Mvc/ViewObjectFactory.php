<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      1.0.0
 */

namespace Ascmvc\Mvc;

class ViewObjectFactory
{
    protected static $templateInstance;

    // @codeCoverageIgnoreStart
    protected function __construct()
    {
    }

    protected function __clone()
    {
    }
    // @codeCoverageIgnoreEnd

    public static function getInstance(array $baseConfig)
    {
        if (!self::$templateInstance) {
            if ($baseConfig['templateManager'] === 'Plates') {
                self::$templateInstance = new \League\Plates\Engine($baseConfig['templates']['templateDir']);
            } elseif ($baseConfig['templateManager'] === 'Twig') {
                $loader = new \Twig_Loader_Filesystem($baseConfig['templates']['templateDir']);
                if ($baseConfig['env'] === 'production') {
                    self::$templateInstance = new \Twig_Environment($loader, array(
                        'cache' => $baseConfig['templates']['compileDir'],
                    ));
                } else {
                    self::$templateInstance = new \Twig_Environment($loader, array(
                        'cache' => false,
                    ));
                }
            } elseif ($baseConfig['templateManager'] === 'Smarty') {
                self::$templateInstance = new \Smarty();
                self::$templateInstance->setTemplateDir($baseConfig['templates']['templateDir']);
                self::$templateInstance->setCompileDir($baseConfig['templates']['compileDir']);
                self::$templateInstance->setConfigDir($baseConfig['templates']['configDir']);

                if ($baseConfig['env'] === 'production') {
                    self::$templateInstance->setCacheDir($baseConfig['templates']['cacheDir']);
                    self::$templateInstance->caching = true;
                } else {
                    self::$templateInstance->caching = false;
                }
            }
        }

        return self::$templateInstance;
    }
}
