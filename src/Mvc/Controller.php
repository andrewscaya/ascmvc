<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.1.1
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      1.0.0
 */

namespace Ascmvc\Mvc;

use Ascmvc\AbstractController;
use Ascmvc\AscmvcEventManagerListenerInterface;

/**
 * Class Controller
 *
 * The Controller class extends the AbstractController and
 * implements the AscmvcEventManagerListenerInterface.
 *
 */
class Controller extends AbstractController implements AscmvcEventManagerListenerInterface
{
    /**
     * Controller constructor.
     *
     * @param array $baseConfig
     */
    public function __construct(array $baseConfig)
    {
        $this->baseConfig = $baseConfig;

        $this->view = $this->baseConfig['view'];
    }

    // @codeCoverageIgnoreStart
    /**
     * OnBootstrap listener method.
     *
     * @param AscmvcEvent $event
     *
     * @return mixed|void
     */
    public static function onBootstrap(AscmvcEvent $event)
    {
    }

    /**
     * OnDispatch listener method.
     *
     * @param AscmvcEvent $event
     *
     * @return mixed|void
     */
    public function onDispatch(AscmvcEvent $event)
    {
    }

    /**
     * OnRender listener method.
     *
     * @param AscmvcEvent $event
     *
     * @return mixed|void
     */
    public function onRender(AscmvcEvent $event)
    {
    }

    /**
     * OnFinish listener method.
     *
     * @param AscmvcEvent $event
     *
     * @return mixed|void
     */
    public function onFinish(AscmvcEvent $event)
    {
    }

    /**
     * The controller's default action.
     *
     * @param null $vars
     *
     * @return mixed|void
     */
    public function indexAction($vars = null)
    {
    }
    // @codeCoverageIgnoreEnd
}
