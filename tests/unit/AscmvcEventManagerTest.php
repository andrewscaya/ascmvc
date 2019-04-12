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

use \Application\Controllers\FakeController;
use Ascmvc\EventSourcing\EventDispatcher;
use Ascmvc\Mvc\App;
use Ascmvc\Mvc\AscmvcEvent;
use Ascmvc\Mvc\AscmvcEventManagerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AscmvcEventManagerTest extends TestCase
{
    public function tearDown()
    {
        \Mockery::close();

        parent::tearDown();
    }

    public function testDefaultMVCEventsTriggersControllerOnBootstrap()
    {
        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['events'] = [
            // PSR-14 compliant Event Bus.
            'psr14_event_dispatcher' => \Ascmvc\EventSourcing\EventDispatcher::class,
            // Different read and write connections allow for simplified (!) CQRS. :)
            'read_conn_name' => 'dem1',
            'write_conn_name' => 'dem1',
        ];

        $eventManager = AscmvcEventManagerFactory::create();

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_BOOTSTRAP);

        $shortCircuit = function ($response) use ($ascmvcEvent) {
            if ($response instanceof Response) {
                return true;
            } else {
                return false;
            }
        };

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $ascmvcEvent->setApplication($app);

        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);

        $response = $result->last();
        $contents = $response->getBody()->__toString();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('FakeController onBootstrap', $contents);
    }

    public function testDefaultMVCEventsTriggersControllerOnDispatch()
    {
        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $baseConfig['events'] = [
            // PSR-14 compliant Event Bus.
            'psr14_event_dispatcher' => \Ascmvc\EventSourcing\EventDispatcher::class,
            // Different read and write connections allow for simplified (!) CQRS. :)
            'read_conn_name' => 'dem1',
            'write_conn_name' => 'dem1',
        ];

        $eventManager = AscmvcEventManagerFactory::create();

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_DISPATCH);

        $shortCircuit = function ($response) use ($ascmvcEvent) {
            if ($response instanceof Response) {
                return true;
            } else {
                return false;
            }
        };

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $eventDispatcher = new EventDispatcher($app);

        $controller = new FakeController($baseConfig, $eventDispatcher);
        $app->setController($controller);
        $ascmvcEvent->setApplication($app);

        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);

        $response = $result->last();
        $contents = $response->getBody()->__toString();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('FakeController onDispatch', $contents);
    }

    public function testDefaultMVCEventsTriggersControllerOnRender()
    {
        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $baseConfig['events'] = [
            // PSR-14 compliant Event Bus.
            'psr14_event_dispatcher' => \Ascmvc\EventSourcing\EventDispatcher::class,
            // Different read and write connections allow for simplified (!) CQRS. :)
            'read_conn_name' => 'dem1',
            'write_conn_name' => 'dem1',
        ];

        $eventManager = AscmvcEventManagerFactory::create();

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_RENDER);

        $shortCircuit = function ($response) use ($ascmvcEvent) {
            if ($response instanceof Response) {
                return true;
            } else {
                return false;
            }
        };

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $eventDispatcher = new EventDispatcher($app);

        $controller = new FakeController($baseConfig, $eventDispatcher);
        $app->setController($controller);
        $ascmvcEvent->setApplication($app);

        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);

        $response = $result->last();
        $contents = $response->getBody()->__toString();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('FakeController onRender', $contents);
    }

    public function testDefaultMVCEventsTriggersControllerOnFinish()
    {
        ob_start();

        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $requestMock = \Mockery::mock(
            'overload:' . ServerRequest::class,
            'overload:' . ServerRequestInterface::class
        );
        $requestMock
            ->shouldReceive('getProtocolVersion')
            ->once()
            ->andReturn('1.1');

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $baseConfig['events'] = [
            // PSR-14 compliant Event Bus.
            'psr14_event_dispatcher' => \Ascmvc\EventSourcing\EventDispatcher::class,
            // Different read and write connections allow for simplified (!) CQRS. :)
            'read_conn_name' => 'dem1',
            'write_conn_name' => 'dem1',
        ];

        $eventManager = AscmvcEventManagerFactory::create();

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_FINISH);

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $eventDispatcher = new EventDispatcher($app);

        $controller = new FakeController($baseConfig, $eventDispatcher);
        $app->setController($controller);
        $app->setRequest($requestMock);
        $ascmvcEvent->setApplication($app);

        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $eventManager->triggerEvent($ascmvcEvent);

        $this->assertInstanceOf(Response::class, $app->getResponse());
        $this->assertSame('FakeController onFinish', $this->getActualOutput());

        ob_end_clean();
    }

    public function testDefaultMVCEventsCanBeTriggeredWithArrayAsControllerOutput()
    {
        ob_start();

        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $requestMock = \Mockery::mock(
            'overload:' . ServerRequest::class,
            'overload:' . ServerRequestInterface::class
        );
        $requestMock
            ->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['REQUEST_URI' => '/faker/eventmanagertestarray/eventmanagertestfakeparams1']);
        $requestMock
            ->shouldReceive('getMethod')
            ->once()
            ->andReturn('GET');
        $requestMock
            ->shouldReceive('getParsedBody')
            ->once();
        $requestMock
            ->shouldReceive('getUploadedFiles')
            ->once();
        $requestMock
            ->shouldReceive('getCookieParams')
            ->once();
        $requestMock
            ->shouldReceive('getProtocolVersion')
            ->once()
            ->andReturn('1.1');

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['events'] = [
            // PSR-14 compliant Event Bus.
            'psr14_event_dispatcher' => \Ascmvc\EventSourcing\EventDispatcher::class,
            // Different read and write connections allow for simplified (!) CQRS. :)
            'read_conn_name' => 'dem1',
            'write_conn_name' => 'dem1',
        ];

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/faker[/{action}/{test}]',
                'specialmodule/fake',
            ],
        ];

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent = $app->getEvent();

        $shortCircuit = function ($response) use ($ascmvcEvent) {
            if ($response instanceof Response) {
                return true;
            } else {
                return false;
            }
        };

        $eventManager = $app->getEventManager();

        // Initial onBootstrap event was set during the application's initialization
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);
        $this->assertTrue($result->stopped());

        $ascmvcEvent->setName(AscmvcEvent::EVENT_ROUTE);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEvent($ascmvcEvent);
        $this->assertFalse($result->stopped());
        $this->assertNull($result->last());

        $ascmvcEvent->setName(AscmvcEvent::EVENT_DISPATCH);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);
        $controllerOutput = $result->last();

        $this->assertFalse($result->stopped());

        $this->assertSame(
            'AscmvcEventManagerTest_testDefaultMVCEventsCanBeTriggeredWithArrayAsControllerOutput_eventmanagertestfakeparams1',
            $controllerOutput['results']
        );

        $app->setControllerOutput($controllerOutput);

        $ascmvcEvent->setName(AscmvcEvent::EVENT_RENDER);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);
        $response = $result->last();

        $this->assertInstanceOf(Response::class, $response);

        $app->setResponse($response);

        $ascmvcEvent->setName(AscmvcEvent::EVENT_FINISH);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $eventManager->triggerEvent($ascmvcEvent);

        $this->assertSame(
            "<html>\n"
            . "<head>\n"
            . "</head>\n"
            . "<body>\n"
            . "<!-- Plates template -->\n"
            . "AscmvcEventManagerTest_testDefaultMVCEventsCanBeTriggeredWithArrayAsControllerOutput_eventmanagertestfakeparams1</body>\n"
            . "</html>",
            $this->getActualOutput()
        );

        ob_clean();
    }

    public function testDefaultMVCEventsCanBeTriggeredWithResponseAsControllerOutput()
    {
        ob_start();

        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $requestMock = \Mockery::mock(
            'overload:' . ServerRequest::class,
            'overload:' . ServerRequestInterface::class
        );
        $requestMock
            ->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['REQUEST_URI' => '/faker/eventmanagertestresponse/eventmanagertestfakeparams2']);
        $requestMock
            ->shouldReceive('getMethod')
            ->once()
            ->andReturn('GET');
        $requestMock
            ->shouldReceive('getParsedBody')
            ->once();
        $requestMock
            ->shouldReceive('getUploadedFiles')
            ->once();
        $requestMock
            ->shouldReceive('getCookieParams')
            ->once();
        $requestMock
            ->shouldReceive('getProtocolVersion')
            ->once()
            ->andReturn('1.1');

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['events'] = [
            // PSR-14 compliant Event Bus.
            'psr14_event_dispatcher' => \Ascmvc\EventSourcing\EventDispatcher::class,
            // Different read and write connections allow for simplified (!) CQRS. :)
            'read_conn_name' => 'dem1',
            'write_conn_name' => 'dem1',
        ];

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/faker[/{action}/{test}]',
                'specialmodule/fake',
            ],
        ];

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent = $app->getEvent();

        $shortCircuit = function ($response) use ($ascmvcEvent) {
            if ($response instanceof Response) {
                return true;
            } else {
                return false;
            }
        };

        $eventManager = $app->getEventManager();

        // Initial onBootstrap event was set during the application's initialization
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);
        $this->assertTrue($result->stopped());

        $ascmvcEvent->setName(AscmvcEvent::EVENT_ROUTE);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEvent($ascmvcEvent);
        $this->assertFalse($result->stopped());
        $this->assertNull($result->last());

        $ascmvcEvent->setName(AscmvcEvent::EVENT_DISPATCH);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);
        $controllerOutput = $result->last();

        $this->assertTrue($result->stopped());

        $this->assertInstanceOf(Response::class, $controllerOutput);

        $app->setResponse($controllerOutput);

        $ascmvcEvent->setName(AscmvcEvent::EVENT_FINISH);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $eventManager->triggerEvent($ascmvcEvent);

        $this->assertSame(
            'AscmvcEventManagerTest_testDefaultMVCEventsCanBeTriggeredWithResponseAsControllerOutput_eventmanagertestfakeparams2',
            $this->getActualOutput()
        );

        ob_end_clean();
    }

    public function testDefaultMVCEventsCanBeTriggeredWithStringAsControllerOutput()
    {
        ob_start();

        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $requestMock = \Mockery::mock(
            'overload:' . ServerRequest::class,
            'overload:' . ServerRequestInterface::class
        );
        $requestMock
            ->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['REQUEST_URI' => '/faker/eventmanagerteststring/eventmanagertestfakeparams3']);
        $requestMock
            ->shouldReceive('getMethod')
            ->once()
            ->andReturn('GET');
        $requestMock
            ->shouldReceive('getParsedBody')
            ->once();
        $requestMock
            ->shouldReceive('getUploadedFiles')
            ->once();
        $requestMock
            ->shouldReceive('getCookieParams')
            ->once();
        $requestMock
            ->shouldReceive('getProtocolVersion')
            ->once()
            ->andReturn('1.1');

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['events'] = [
            // PSR-14 compliant Event Bus.
            'psr14_event_dispatcher' => \Ascmvc\EventSourcing\EventDispatcher::class,
            // Different read and write connections allow for simplified (!) CQRS. :)
            'read_conn_name' => 'dem1',
            'write_conn_name' => 'dem1',
        ];

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/faker[/{action}/{test}]',
                'specialmodule/fake',
            ],
        ];

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent = $app->getEvent();

        $shortCircuit = function ($response) use ($ascmvcEvent) {
            if ($response instanceof Response) {
                return true;
            } else {
                return false;
            }
        };

        $eventManager = $app->getEventManager();

        // Initial onBootstrap event was set during the application's initialization
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);
        $this->assertTrue($result->stopped());

        $ascmvcEvent->setName(AscmvcEvent::EVENT_ROUTE);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEvent($ascmvcEvent);
        $this->assertFalse($result->stopped());
        $this->assertNull($result->last());

        $ascmvcEvent->setName(AscmvcEvent::EVENT_DISPATCH);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);
        $controllerOutput = $result->last();

        $this->assertFalse($result->stopped());

        $this->assertSame(
            'AscmvcEventManagerTest_testDefaultMVCEventsCanBeTriggeredWithStringAsControllerOutput_eventmanagertestfakeparams3',
            $controllerOutput
        );

        $app->setControllerOutput($controllerOutput);

        $ascmvcEvent->setName(AscmvcEvent::EVENT_RENDER);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);
        $response = $result->last();

        $this->assertInstanceOf(Response::class, $response);

        $app->setResponse($response);

        $ascmvcEvent->setName(AscmvcEvent::EVENT_FINISH);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $eventManager->triggerEvent($ascmvcEvent);

        $this->assertSame(
            'AscmvcEventManagerTest_testDefaultMVCEventsCanBeTriggeredWithStringAsControllerOutput_eventmanagertestfakeparams3',
            $this->getActualOutput()
        );

        ob_end_clean();
    }

    public function testDefaultMVCEventsCanBeTriggeredWithShortCircuitOnBootstrap()
    {
        ob_start();

        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $requestMock = \Mockery::mock(
            'overload:' . ServerRequest::class,
            'overload:' . ServerRequestInterface::class
        );
        $requestMock
            ->shouldReceive('getServerParams')
            ->once()
            ->andReturn(['REQUEST_URI' => '/fake']);
        $requestMock
            ->shouldReceive('getMethod')
            ->once()
            ->andReturn('GET');
        $requestMock
            ->shouldReceive('getParsedBody')
            ->once();
        $requestMock
            ->shouldReceive('getUploadedFiles')
            ->once();
        $requestMock
            ->shouldReceive('getCookieParams')
            ->once();
        $requestMock
            ->shouldReceive('getProtocolVersion')
            ->once()
            ->andReturn('1.1');

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['events'] = [
            // PSR-14 compliant Event Bus.
            'psr14_event_dispatcher' => \Ascmvc\EventSourcing\EventDispatcher::class,
            // Different read and write connections allow for simplified (!) CQRS. :)
            'read_conn_name' => 'dem1',
            'write_conn_name' => 'dem1',
        ];

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/fake[/{action}]',
                'fake',
            ],
        ];

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent = $app->getEvent();

        $shortCircuit = function ($response) use ($ascmvcEvent) {
            if ($response instanceof Response) {
                return true;
            } else {
                return false;
            }
        };

        $eventManager = $app->getEventManager();

        // Initial onBootstrap event was set during the application's initialization
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);
        $response = $result->last();
        $this->assertTrue($result->stopped());

        $this->assertInstanceOf(Response::class, $response);

        $app->setResponse($response);

        $ascmvcEvent->setName(AscmvcEvent::EVENT_FINISH);
        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $eventManager->triggerEvent($ascmvcEvent);

        $this->assertSame('FakeController onBootstrap', $this->getActualOutput());

        ob_end_clean();
    }
}
