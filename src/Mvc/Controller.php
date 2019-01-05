<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      1.0.0
 */

namespace Ascmvc\Mvc;

use Ascmvc\AbstractApp;
use Ascmvc\AbstractController;
use Ascmvc\AscmvcEventManagerListenerInterface;

class Controller extends AbstractController implements AscmvcEventManagerListenerInterface
{

    public function __construct(array $baseConfig)
    {
        $this->baseConfig = $baseConfig;

        $this->view = $this->baseConfig['view'];
    }

    // @codeCoverageIgnoreStart
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
    // @codeCoverageIgnoreEnd
}
