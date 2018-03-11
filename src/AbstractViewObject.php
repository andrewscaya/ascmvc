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
     * @param void.
     *
     * @return AbstractViewObject
     */
    public static function getInstance()
    {

    }

}