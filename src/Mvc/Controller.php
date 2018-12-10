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

use Ascmvc\AbstractApp;
use Ascmvc\AbstractController;


class Controller extends AbstractController {

    public function __construct(array $baseConfig)
    {
		$this->baseConfig = $baseConfig;
		
        $this->config($this->baseConfig);
        
        $this->view = $this->baseConfig['view'];
    }
    
    public function config(array $baseConfig)
    {
    
    }
    
    public static function preboot(AbstractApp &$app)
    {
        
    }
    
    public static function postboot(AbstractApp &$app)
    {
        
    }
    
    public function predispatch()
    {
        
    }
    
    public function postdispatch()
    {
        
    }
    
    public function preresponse()
    {
        
    }

    public function indexAction($vars = null)
    {
        
    }

}
