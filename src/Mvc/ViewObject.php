<?php
/**
 * ASC LightMVC
 *
 * @package    ASC LightMVC
 * @author     Andrew Caya
 * @link       https://github.com/andrewscaya
 * @version    1.0.0
 * @license    http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Ascmvc\Mvc;

use Ascmvc\AbstractViewObject;


class ViewObject extends AbstractViewObject {

    protected function __construct()
    {
        
    }

    protected function __clone()
    {
    
    }

    public static function getInstance(array $baseConfig)
    {
        if(!self::$templateInstance) {

            if($baseConfig['templateManager'] === 'Twig') {
                $loader = new \Twig_Loader_Filesystem($baseConfig['templates']['templateDir']);
                if($baseConfig['env'] === 'production') {
                    self::$templateInstance = new \Twig_Environment($loader, array(
                        'cache' => $baseConfig['templates']['compileDir'],
                    ));
                } else {
                    self::$templateInstance = new \Twig_Environment($loader, array(
                        'cache' => false,
                    ));
                }
            } elseif($baseConfig['templateManager'] === 'Plates') {
                self::$templateInstance = new \League\Plates\Engine($baseConfig['templates']['templateDir']);
            } elseif($baseConfig['templateManager'] === 'Smarty') {
                self::$templateInstance = new \Smarty();
                self::$templateInstance->setTemplateDir($baseConfig['templates']['templateDir']);
                self::$templateInstance->setCompileDir($baseConfig['templates']['compileDir']);
                self::$templateInstance->setConfigDir($baseConfig['templates']['configDir']);

                if($baseConfig['env'] === 'production') {
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
