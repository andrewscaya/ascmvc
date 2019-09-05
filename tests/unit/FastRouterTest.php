<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.1
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.0.0
 */

namespace AscmvcTest;

use Application\Controllers\C404Controller;
use Application\Controllers\C405Controller;
use Application\Controllers\FakeController;
use Ascmvc\Mvc\App;
use Ascmvc\Mvc\AscmvcEvent;
use Ascmvc\Mvc\ControllerManager;
use Ascmvc\Mvc\FastRouter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FastRouterTest extends TestCase
{
    public function tearDown()
    {
        \Mockery::close();

        parent::tearDown();
    }

    public function testRouterResolutionInDevelopmentEnvironment()
    {
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

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/fake[/{action}]',
                'fake',
            ],
        ];

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_ROUTE);

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent->setApplication($app);

        $fastRouter = new FastRouter($ascmvcEvent);

        $app->setRouter($fastRouter);

        $fastRouter->resolve();

        $this->assertSame(['REQUEST_URI' => '/fake'], $fastRouter->getRequestURI());

        $this->assertInstanceOf(ControllerManager::class, $app->getControllerManager());

        $this->assertInstanceOf(FakeController::class, $app->getController());
    }

    public function testRouterResolutionInDevelopmentEnvironmentWithMultipleRoutes()
    {
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

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/fake[/{action}]',
                'fake',
            ],
            1 => [
                'GET',
                '/specialmodule/fake[/{action}]',
                'specialmodule/fake',
            ],
        ];

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_ROUTE);

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent->setApplication($app);

        $fastRouter = new FastRouter($ascmvcEvent);

        $app->setRouter($fastRouter);

        $fastRouter->resolve();

        $this->assertSame(['REQUEST_URI' => '/fake'], $fastRouter->getRequestURI());

        $this->assertInstanceOf(ControllerManager::class, $app->getControllerManager());

        $this->assertInstanceOf(FakeController::class, $app->getController());
    }

    public function testRouterResolutionInProductionEnvironment()
    {
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

        $baseConfig['env'] = 'production';

        $baseConfig['view'] = [];

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

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_ROUTE);

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent->setApplication($app);

        $fastRouter = new FastRouter($ascmvcEvent);

        $app->setRouter($fastRouter);

        $fastRouter->resolve();

        unlink(
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'cache'
            . DIRECTORY_SEPARATOR
            . 'routes.cache'
        );

        $this->assertInstanceOf(ControllerManager::class, $app->getControllerManager());

        $this->assertInstanceOf(FakeController::class, $app->getController());
    }

    public function testRouterResolutionInProductionEnvironmentWithMultipleRoutes()
    {
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

        $baseConfig['env'] = 'production';

        $baseConfig['view'] = [];

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
            1 => [
                'GET',
                '/specialmodule/fake[/{action}]',
                'specialmodule/fake',
            ],
        ];



        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_ROUTE);

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent->setApplication($app);

        $fastRouter = new FastRouter($ascmvcEvent);

        $app->setRouter($fastRouter);

        $fastRouter->resolve();

        unlink(
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'cache'
            . DIRECTORY_SEPARATOR
            . 'routes.cache'
        );

        $this->assertSame(['REQUEST_URI' => '/fake'], $fastRouter->getRequestURI());

        $this->assertInstanceOf(ControllerManager::class, $app->getControllerManager());

        $this->assertInstanceOf(FakeController::class, $app->getController());
    }

    public function testRouterResolutionInDevelopmentEnvironmentWithControllerFoundRoute()
    {
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

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/fake[/{action}]',
                'fake',
            ],
        ];

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_ROUTE);

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent->setApplication($app);

        $fastRouter = new FastRouter($ascmvcEvent);

        $app->setRouter($fastRouter);

        $fastRouter->resolve();

        $this->assertInstanceOf(ControllerManager::class, $app->getControllerManager());

        $this->assertInstanceOf(FakeController::class, $app->getController());
    }

    public function testRouterResolutionInDevelopmentEnvironmentWithControllerNotFoundRoute()
    {
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
            ->andReturn(['REQUEST_URI' => '/wrong']);
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

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/fake[/{action}]',
                'fake',
            ],
        ];

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_ROUTE);

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent->setApplication($app);

        $fastRouter = new FastRouter($ascmvcEvent);

        $app->setRouter($fastRouter);

        $fastRouter->resolve();

        $this->assertInstanceOf(ControllerManager::class, $app->getControllerManager());

        $this->assertInstanceOf(C404Controller::class, $app->getController());
    }

    public function testRouterResolutionInDevelopmentEnvironmentWithMethodFoundRoute()
    {
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
            ->andReturn(['REQUEST_URI' => '/fake/index']);
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

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/fake[/{action}]',
                'fake',
            ],
        ];

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_ROUTE);

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent->setApplication($app);

        $fastRouter = new FastRouter($ascmvcEvent);

        $app->setRouter($fastRouter);

        $fastRouter->resolve();

        $this->assertInstanceOf(ControllerManager::class, $app->getControllerManager());

        $this->assertInstanceOf(FakeController::class, $app->getController());
    }

    public function testRouterResolutionInDevelopmentEnvironmentWithMethodNotFoundRoute()
    {
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
            ->andReturn(['REQUEST_URI' => '/fake/wrong']);
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

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/fake[/{action}]',
                'fake',
            ],
        ];

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_ROUTE);

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent->setApplication($app);

        $fastRouter = new FastRouter($ascmvcEvent);

        $app->setRouter($fastRouter);

        $fastRouter->resolve();

        $this->assertInstanceOf(ControllerManager::class, $app->getControllerManager());

        $this->assertInstanceOf(C404Controller::class, $app->getController());
    }

    public function testRouterResolutionInDevelopmentEnvironmentWithMethodNotAllowedRoute()
    {
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

        $baseConfig['routes'] = [
            0 => [
                'POST',
                '/fake[/{action}]',
                'fake',
            ],
        ];

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_ROUTE);

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent->setApplication($app);

        $fastRouter = new FastRouter($ascmvcEvent);

        $app->setRouter($fastRouter);

        $fastRouter->resolve();

        $this->assertInstanceOf(ControllerManager::class, $app->getControllerManager());

        $this->assertInstanceOf(C405Controller::class, $app->getController());
    }
}
