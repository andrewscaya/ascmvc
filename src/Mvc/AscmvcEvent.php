<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    4.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace Ascmvc\Mvc;

use Ascmvc\AbstractApp;
use Laminas\EventManager\Event;

/**
 * Class AscmvcEvent
 *
 * The class AscmvcEvent extends the Laminas\EventManager\Event class and
 * adds logic that is specific to this MVC.
 */
class AscmvcEvent extends Event
{

    /**#@+
     * Mvc events triggered by eventmanager
     */
    const EVENT_BOOTSTRAP      = 'bootstrap';
    const EVENT_ROUTE          = 'route';
    const EVENT_DISPATCH       = 'dispatch';
    const EVENT_RENDER         = 'render';
    const EVENT_FINISH         = 'finish';
    /**#@-*/

    /**
     * The instance of the application.
     *
     * @var AbstractApp
     */
    protected $application;

    /**
     * Returns the instance of the application.
     *
     * @return AbstractApp
     */
    public function getApplication() : AbstractApp
    {
        return $this->application;
    }

    /**
     * Stores the instance of the application.
     *
     * @param AbstractApp $application
     *
     * @return AbstractApp
     */
    public function setApplication(AbstractApp &$application) : AbstractApp
    {
        $this->application = $application;

        return $this->application;
    }
}
