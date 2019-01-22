<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.2
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.0.0
 */

namespace AscmvcTest;

use Ascmvc\Mvc\App;
use Ascmvc\Mvc\AscmvcEvent;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AscmvcEventTest extends TestCase
{
    public function testSetAndGetApplicationInstance()
    {
        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_BOOTSTRAP);

        $app = App::getInstance();

        $ascmvcEvent->setApplication($app);

        $this->assertInstanceOf(App::class, $ascmvcEvent->getApplication());
    }
}
