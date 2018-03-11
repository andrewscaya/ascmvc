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


abstract class AbstractModelObject
{

    /**
     * Array contains instances of Doctrine objects (DBAL or ORM).
     *
     * @var array|null
     */
    protected static $doctrineInstance;

    /**
     * Protected method : this class cannot be instantiated by the new keyword
     * because it is a Singleton.
     *
     * @param void.
     *
     * @return void.
     */
    abstract protected function __construct();

    /**
     * Protected method : this class cannot be copied because it is a Singleton.
     *
     * @param void.
     *
     * @return void.
     */
    abstract protected function __clone();

    /**
     * Static method : returns the Singleton instance of this class.
     *
     * @param string $connType
     * @param string $connName
     * @param Array $params
     *
     * @return AbstractModelObject
     */
    public static function getInstance($connType, $connName, Array $params)
    {

    }

}