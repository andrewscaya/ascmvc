<?php

namespace Ascmvc\Mvc;

use Ascmvc\AbstractApp;
use Zend\EventManager\Event;


class AscmvcEvent extends Event {
	
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
