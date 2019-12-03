<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.3.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      1.0.0
 */

namespace Ascmvc\Mvc;

/**
 * Class ViewObjectFactory
 *
 * The ViewObjectFactory creates an instance of the Plates, Twig or Smarty template manager,
 * according to specified configuration.
 *
 */
class ViewObjectFactory
{
    /**
     * Contains an instance of a template manager.
     *
     * @var \League\Plates\Engine|\Smarty|\Twig_Environment
     */
    protected static $templateInstance;

    // @codeCoverageIgnoreStart

    /**
     * ViewObjectFactory constructor.
     */
    protected function __construct()
    {
    }

    /**
     * ViewObjectFactory clone method.
     */
    protected function __clone()
    {
    }
    // @codeCoverageIgnoreEnd

    /**
     * Gets a Singleton instance of the Doctrine class.
     *
     * @param array $baseConfig
     *
     * @return \League\Plates\Engine|\Smarty|\Twig_Environment
     */
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
