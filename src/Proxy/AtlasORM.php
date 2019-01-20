<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.1
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace Ascmvc\Proxy;

use Atlas\Orm\Atlas;

class AtlasORM extends Atlas
{
    public static function create(...$args) : Atlas
    {
        return parent::new($args);
    }
}
