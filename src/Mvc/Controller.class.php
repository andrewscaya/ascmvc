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


class Controller extends AbstractController {
    
    /**
     * Initializes this class by assigning the objects and arrays
     * contained in the referenced App object to the corresponding
     * properties.
     *
     * @param Object:App &$app.
     *
     * @return void.
     */
    public function __construct(App &$app)
    {
        $this->app = $app;
        
        $this->app->setController($this);
        
        $this->serviceManager = $this->app->getServiceManager();
        
        $this->viewObject = $this->app->getViewObject();
        
        $this->config($this->app);
        
        $this->baseConfig = $this->app->getBaseConfig();
        
        $this->view = $this->baseConfig['view'];
    }
    
    public static function config(App &$app)
    {
    
    }
    
    public static function preboot(App &$app)
    {
        
    }
    
    public static function postboot(App &$app)
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