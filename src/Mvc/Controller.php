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

    public function __construct(AbstractApp &$app)
    {
        $this->app = $app;
        
        $this->app->setController($this);
        
        $this->serviceManager = $this->app->getServiceManager();
        
        $this->viewObject = $this->app->getViewObject();
        
        $this->config($this->app);
        
        $this->baseConfig = $this->app->getBaseConfig();
        
        $this->view = $this->baseConfig['view'];
    }
    
    public static function config(AbstractApp &$app)
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

    public function indexAction()
    {
        
    }

}