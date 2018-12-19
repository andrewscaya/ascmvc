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
use Ascmvc\AscmvcEventManagerListenerInterface;


class Controller extends AbstractController implements AscmvcEventManagerListenerInterface {

    public function __construct(array $baseConfig)
    {
		$this->baseConfig = $baseConfig;
        
        $this->view = $this->baseConfig['view'];
    }
    
    public static function onBootstrap(AscmvcEvent $event)
    {

    }
    
    public function onDispatch(AscmvcEvent $event)
    {
        
    }
    
    public function onRender(AscmvcEvent $event)
    {
        
    }

    public function onFinish(AscmvcEvent $event)
    {

    }
    
    public function indexAction($vars = null)
    {
        
    }

}
