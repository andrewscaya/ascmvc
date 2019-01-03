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
 * @since      2.0.0
 */

namespace Ascmvc\Mvc;

use Zend\EventManager\SharedEventManager;

class AscmvcEventManagerFactory
{

    public static function create()
    {
        $shared = new SharedEventManager();
        return new AscmvcEventManager($shared);
    }
}
