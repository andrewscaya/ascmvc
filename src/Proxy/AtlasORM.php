<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.1.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace Ascmvc\Proxy;

use Atlas\Orm\Atlas;

/**
 * Class AtlasORM
 *
 * Proxy to class \Atlas\Orm\Atlas
 */
class AtlasORM extends Atlas
{
    /**
     * Wrapper function to the Atlas::new(...$args) method.
     *
     * @param array ...$args
     *
     * @return Atlas
     */
    public static function create(...$args) : Atlas
    {
        return parent::new($args);
    }
}
