<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.0
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      1.0.0
 */

namespace Ascmvc;

abstract class AbstractViewObject
{

    /**
     * Contains a Template Engine instance.
     *
     * @var AbstractViewObject|null
     */
    protected static $templateInstance;

    /**
     * Protected method : this class cannot be instantiated by the new keyword
     * because it is a Singleton.
     *
     * @param void.
     *
     * @return void.
     */
    protected abstract function __construct();

    /**
     * Protected method : this class cannot be copied because it is a Singleton.
     *
     * @param void.
     *
     * @return void.
     */
    protected abstract function __clone();

    /**
     * Static method : returns the Singleton instance of this class.
     *
     * @param array $baseConfig.
     *
     * @return AbstractViewObject
     */
    public static function getInstance(array $baseConfig)
    {
    }
}
