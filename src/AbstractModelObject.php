<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.3
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      1.0.0
 */

namespace Ascmvc;

/**
 * Class AbstractModelObject
 *
 * The abstract AbstractModelObject class is the blueprint for the MVC's main model objects.
 *
 * *Description* The AbstractModelObject class is the one that needs to be extended
 * in order to create a LightMVC model object.
 */
abstract class AbstractModelObject
{

    /**
     * Array contains instances of model objects (DBAL or ORM).
     *
     * @var array|null
     */
    protected static $modelInstance;

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
     * Static method : returns the Singleton instance of the model class.
     *
     * @param string $connType
     * @param string $connName
     * @param array $params
     *
     * @return mixed
     */
    public static function getInstance($connType, $connName, Array $params)
    {
    }
}
