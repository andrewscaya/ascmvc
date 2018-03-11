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


class Smarty extends AbstractViewObject {

    protected function __construct()
    {
        
    }

    protected function __clone()
    {
    
    }

    public static function getInstance()
    {
        if(!self::$templateInstance) {
        
            self::$templateInstance = new \Smarty();
        
        }
        
        return self::$templateInstance;
    }
    
}