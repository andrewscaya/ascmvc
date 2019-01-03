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

use Ascmvc\AbstractApp;
use Zend\EventManager\Event;

class AscmvcEvent extends Event
{

    /**#@+
     * Mvc events triggered by eventmanager
     */
    const EVENT_BOOTSTRAP      = 'bootstrap';
    const EVENT_ROUTE          = 'route';
    const EVENT_DISPATCH       = 'dispatch';
    const EVENT_DISPATCH_ERROR = 'dispatch.error';
    const EVENT_RENDER         = 'render';
    const EVENT_RENDER_ERROR   = 'render.error';
    const EVENT_FINISH         = 'finish';
    /**#@-*/

    protected $application;

    public function getApplication()
    {
        return $this->application;
    }

    public function setApplication(AbstractApp &$application)
    {
        $this->application = $application;
        return $this->application;
    }
}
