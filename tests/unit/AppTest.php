<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.2
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.0.0
 */

namespace AscmvcTest;

use Application\Log\Repository\EventLogRepository;
use Ascmvc\Mvc\App;
use Ascmvc\Mvc\AscmvcEvent;
use Ascmvc\Session\SessionManager;
use Atlas\Orm\Atlas;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Stratigility\MiddlewarePipe;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AppTest extends TestCase
{
    public function testGetAppInstance()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $app = App::getInstance();

        $this->assertInstanceOf(App::class, $app);
    }

    public function testAppendBaseConfigMethod()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->appendBaseConfig('test', ['test1']);

        $this->assertArrayHasKey('test', $app->getBaseConfig());

        $this->assertFalse($app->isSwoole());
    }

    public function testBootMethodLoadsConfigurationFiles()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $_SERVER['HTTP_HOST'] = 'localhost';

        $app = App::getInstance();

        $baseConfig = $app->boot();

        $this->assertSame('http://localhost/', URLBASEADDR);

        $this->assertSame(
            [
                'BASEDIR' => BASEDIR,
                'URLBASEADDR' => 'http://localhost/',
                'appFolder' => 'app',
                'env' => 'development',
                'appName' => 'The LightMVC Framework Skeleton Test Application',
            ],
            $baseConfig
        );
    }

    public function testBootMethodLoadsLocalConfigurationFiles()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = BASEDIR;

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

        rename(
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'config.local.php.dist',
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'config.local.php'
        );

        $_SERVER['HTTP_HOST'] = 'localhost';

        $app = App::getInstance();

        $baseConfig = $app->boot();

        $this->assertSame('http://localhost/', URLBASEADDR);

        $this->assertSame(
            [
                'BASEDIR' => BASEDIR,
                'URLBASEADDR' => 'http://localhost/',
                'appFolder' => 'app',
                'env' => 'development',
                'appName' => 'The LightMVC Framework Skeleton Test 2 Application',
            ],
            $baseConfig
        );

        rename(
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'config.local.php',
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'config.local.php.dist'
        );
    }

    public function testInitializeMethodLoadsConfigurationFilesWithDoctrine()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $baseConfig['doctrine']['DBAL']['dm2'] = [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'user'     => 'USERNAME',
            'password' => 'PASSWORD',
            'dbname'   => 'DATABASE',
        ];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $serviceManager = $app->getServiceManager();

        $this->assertArrayHasKey('dm2', $serviceManager);
        $this->assertInstanceOf(Connection::class, $serviceManager['dm2']);
    }

    public function testInitializeMethodLoadsConfigurationFilesWithAtlas()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $baseConfig['atlas']['ORM']['aem1'] = [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'user'     => 'USERNAME',
            'password' => 'PASSWORD',
            'dbname'   => 'DATABASE',
        ];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $serviceManager = $app->getServiceManager();

        $this->assertArrayHasKey('aem1', $serviceManager);
        $this->assertInstanceOf(Atlas::class, $serviceManager['aem1']);
    }

    public function testInitializeMethodLoadsConfigurationFilesWithMiddleware()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/test1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $baseConfig['middleware'] = [
            function ($req, $handler) {
                $response = new Response();
                $response->getBody()->write('Hello from callable middleware 1');

                return $response;
            },
        ];

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

        $app->setRequest($requestMock);

        $ascmvcEvent->setApplication($app);

        $app->setEvent($ascmvcEvent);

        $eventManager = $app->getEventManager();

        $serviceManager = $app->getServiceManager();

        $this->assertArrayHasKey('middlewarePipe', $serviceManager);
        $this->assertInstanceOf(MiddlewarePipe::class, $serviceManager['middlewarePipe']);

        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);

        $response = $result->last();
        $contents = $response->getBody()->__toString();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('Hello from callable middleware 1', $contents);
    }

    public function testInitializeMethodLoadsConfigurationFilesWithMiddlewareEmptyPipe()
    {
        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/test1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $baseConfig['middleware'] = [
            function ($req, $handler) {
                return $handler->handle($req);
            },
        ];

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

        $app->setRequest($requestMock);

        $ascmvcEvent->setApplication($app);

        $app->setEvent($ascmvcEvent);

        $eventManager = $app->getEventManager();

        $serviceManager = $app->getServiceManager();

        $this->assertArrayHasKey('middlewarePipe', $serviceManager);
        $this->assertInstanceOf(MiddlewarePipe::class, $serviceManager['middlewarePipe']);

        $ascmvcEvent->stopPropagation(false); // Clear before triggering
        $result = $eventManager->triggerEventUntil($shortCircuit, $ascmvcEvent);

        $response = $result->last();
        $contents = $response->getBody()->__toString();

        $this->assertTrue($result->first());
        $this->assertTrue($result->stopped());
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('FakeController onBootstrap', $contents);
    }

    public function testDislayMethod()
    {
        // Redirect output to command output
        $this->setOutputCallback(function () {
        });

        $requestMock = \Mockery::mock(
            'overload:' . ServerRequest::class,
            'overload:' . ServerRequestInterface::class
        );
        $requestMock
            ->shouldReceive('getProtocolVersion')
            ->once()
            ->andReturn('1.1');

        $app = App::getInstance();

        $app->setRequest($requestMock);

        $response = new Response();
        $response->getBody()->write('This is a test response');
        $response = $response->withHeader('X-Special-Header', 'test');
        $response = $response->withStatus(418);

        $app->display($response);

        $headerList = xdebug_get_headers();

        $responseCode = http_response_code();

        $this->assertSame('This is a test response', $this->getActualOutput());
        $this->assertSame('X-Special-Header: test', $headerList[0]);
        $this->assertEquals(418, $responseCode);
    }

    public function testRenderMethodWithString()
    {
        // Redirect output to command output
        $this->setOutputCallback(function () {
        });

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/test1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $controllerOutput = 'This is the controller output';

        $response = $app->render($controllerOutput);

        $this->assertSame(
            'This is the controller output',
            $response->getBody()->__toString()
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRenderMethodWithPlatesAndSpecialStatusCode()
    {
        ob_start();

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/test1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $controllerOutput = [
            'templatefile' => 'test_index',
            'results' => 'AppTest_Render_Array_Plates',
            'statuscode' => 418
        ];

        $response = $app->render($controllerOutput);

        $this->assertSame(
            "<html>\n"
            . "<head>\n"
            . "</head>\n"
            . "<body>\n"
            . "<!-- Plates template -->\n"
            . "AppTest_Render_Array_Plates</body>\n"
            . "</html>",
            $response->getBody()->__toString()
        );

        $this->assertEquals(418, $response->getStatusCode());
    }

    public function testRenderMethodWithArrayAndPlates()
    {
        ob_start();

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/test1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $controllerOutput = [
            'templatefile' => 'test_index',
            'results' => 'AppTest_Render_Array_Plates',
        ];

        $response = $app->render($controllerOutput);

        $this->assertSame(
            "<html>\n"
            . "<head>\n"
            . "</head>\n"
            . "<body>\n"
            . "<!-- Plates template -->\n"
            . "AppTest_Render_Array_Plates</body>\n"
            . "</html>",
            $response->getBody()->__toString()
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRenderMethodWithArrayAndTwig()
    {
        ob_start();

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/test1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

        $baseConfig['templateManager'] = 'Twig';
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

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $controllerOutput = [
            'templatefile' => 'test_index',
            'results' => 'AppTest_Render_Array_Twig',
        ];

        $response = $app->render($controllerOutput);

        $this->assertSame(
            "<html>\n"
            . "<head>\n"
            . "</head>\n"
            . "<body>\n"
            . "<!-- Twig template -->\n"
            . "AppTest_Render_Array_Twig\n"
            . "</body>\n"
            . "</html>",
            $response->getBody()->__toString()
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRenderMethodWithArrayAndSmarty()
    {
        ob_start();

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/test1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

        $baseConfig['templateManager'] = 'Smarty';
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

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $controllerOutput = [
            'templatefile' => 'test_index',
            'results' => 'AppTest_Render_Array_Smarty',
        ];

        $response = $app->render($controllerOutput);

        $this->assertSame(
            "<html>\n"
            . "<head>\n"
            . "</head>\n"
            . "<body>\n"
            . "<!-- Smarty template -->\n"
            . "AppTest_Render_Array_Smarty\n"
            . "</body>\n"
            . "</html>",
            $response->getBody()->__toString()
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRunMethodWithOnBootstrapShortCircuit()
    {
        // Redirect output to command output
        $this->setOutputCallback(function () {
        });

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/test1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $app->run();

        $this->assertSame('FakeController onBootstrap', $this->getActualOutput());
    }

    public function testRunMethodWithOnDispatchShortCircuit()
    {
        // Redirect output to command output
        $this->setOutputCallback(function () {
        });

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        rename(
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'controllers'
            . DIRECTORY_SEPARATOR
            . 'Application'
            . DIRECTORY_SEPARATOR
            . 'Controllers'
            . DIRECTORY_SEPARATOR
            . 'FakeController.php',
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'controllers'
            . DIRECTORY_SEPARATOR
            . 'Application'
            . DIRECTORY_SEPARATOR
            . 'Controllers'
            . DIRECTORY_SEPARATOR
            . 'FakeController.php.OLD'
        );

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
            ->andReturn(['REQUEST_URI' => '/fake/index/fakeparamsrun1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

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
                '/fake[/{action}[/{test}]]',
                'fakedispatch',
            ],
        ];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent = $app->getEvent();
        $ascmvcEvent->setName(AscmvcEvent::EVENT_ROUTE);
        $ascmvcEvent->setApplication($app);
        $app->setEvent($ascmvcEvent);

        $app->run();

        $this->assertSame('FakedispatchController onDispatch', $this->getActualOutput());

        rename(
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'controllers'
            . DIRECTORY_SEPARATOR
            . 'Application'
            . DIRECTORY_SEPARATOR
            . 'Controllers'
            . DIRECTORY_SEPARATOR
            . 'FakeController.php.OLD',
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'controllers'
            . DIRECTORY_SEPARATOR
            . 'Application'
            . DIRECTORY_SEPARATOR
            . 'Controllers'
            . DIRECTORY_SEPARATOR
            . 'FakeController.php'
        );
    }

    public function testRunMethodWithAllEvents()
    {
        // Redirect output to command output
        $this->setOutputCallback(function () {
        });

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

        rename(
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'controllers'
            . DIRECTORY_SEPARATOR
            . 'Application'
            . DIRECTORY_SEPARATOR
            . 'Controllers'
            . DIRECTORY_SEPARATOR
            . 'FakeController.php',
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'controllers'
            . DIRECTORY_SEPARATOR
            . 'Application'
            . DIRECTORY_SEPARATOR
            . 'Controllers'
            . DIRECTORY_SEPARATOR
            . 'FakeController.php.OLD'
        );

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
            ->andReturn(['REQUEST_URI' => '/fake/index/fakeparamsrun1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

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
                '/fake[/{action}[/{test}]]',
                'fakestandard',
            ],
        ];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent = $app->getEvent();
        $ascmvcEvent->setName(AscmvcEvent::EVENT_ROUTE);
        $ascmvcEvent->setApplication($app);
        $app->setEvent($ascmvcEvent);

        $app->run();

        $this->assertSame(
            'AppTest'
            . '_testControllerManagerWillReturnAppropriateControllerWithDefaultIndexAction'
            . '_fakeparamsrun1',
            $this->getActualOutput()
        );

        rename(
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'controllers'
            . DIRECTORY_SEPARATOR
            . 'Application'
            . DIRECTORY_SEPARATOR
            . 'Controllers'
            . DIRECTORY_SEPARATOR
            . 'FakeController.php.OLD',
            BASEDIR
            . DIRECTORY_SEPARATOR
            . 'controllers'
            . DIRECTORY_SEPARATOR
            . 'Application'
            . DIRECTORY_SEPARATOR
            . 'Controllers'
            . DIRECTORY_SEPARATOR
            . 'FakeController.php'
        );
    }

    public function testRunMethodWithAllEventsWithMergedControllerEventOutput()
    {
        // Redirect output to command output
        $this->setOutputCallback(function () {
        });

        ob_start();

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/fakeevent/index']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

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
                '/fakeevent[/{action}]',
                'fakeevent',
            ],
        ];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent = $app->getEvent();
        $ascmvcEvent->setName(AscmvcEvent::EVENT_ROUTE);
        $ascmvcEvent->setApplication($app);
        $app->setEvent($ascmvcEvent);

        $app->run();

        $actualOutput = ob_get_contents();

        $this->assertSame(
            "<html>\n"
            . "<head>\n"
            . "</head>\n"
            . "<body>\n"
            . "<!-- Plates template -->\n"
            . "FakeeventControllerSTDOUTtestaddedvalue</body>\n"
            . "</html>",
            $actualOutput
        );
    }

    public function testRunMethodWithAllEventsWithMergedControllerEventOutputFromAggregateRootController()
    {
        // Redirect output to command output
        $this->setOutputCallback(function () {
        });

        ob_start();

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/fakeaggregate/index']);
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

        $eventLogRepositoryMock = \Mockery::mock('overload:' . EventLogRepository::class);
        $eventLogRepositoryMock
            ->shouldReceive('commit')
            ->once();

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $baseConfig['doctrine']['ORM']['dem1'] = [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'user'     => 'fwuser',
            'password' => 'testpass',
            'dbname'   => 'fw',
        ];

        $baseConfig['events'] = [
            // PSR-14 compliant Event Bus.
            'psr14_event_dispatcher' => \Ascmvc\EventSourcing\EventDispatcher::class,
            // Different read and write connections allow for simplified (!) CQRS. :)
            'read_conn_name' => 'dem1',
            'write_conn_name' => 'dem1',
        ];

        $baseConfig['eventlog'] = [
            'enabled' => true,
            'doctrine' => [
                'log_conn_name' => 'dem1',
                'entity_name' => \Application\Log\Entity\EventLog::class,
            ],
            // Leave empty to log everything, including the kitchen sink. :)
            // If you you start whitelisting events, it will blacklist everything else by default.
            'log_event_type' => [
                'whitelist' => [
                    \Ascmvc\EventSourcing\Event\WriteAggregateCompletedEvent::class,
                ],
                'blacklist' => [
                    //\Ascmvc\EventSourcing\Event\AggregateEvent::class,
                ],
            ],
        ];

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/fakeaggregate[/{action}]',
                'fakeaggregate',
            ],
        ];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent = $app->getEvent();
        $ascmvcEvent->setName(AscmvcEvent::EVENT_ROUTE);
        $ascmvcEvent->setApplication($app);
        $app->setEvent($ascmvcEvent);

        $app->run();

        $actualOutput = ob_get_contents();

        $this->assertSame(
            "<html>\n"
            . "<head>\n"
            . "</head>\n"
            . "<body>\n"
            . "<!-- Plates template -->\n"
            . "FakeaggregateControllerSTDOUTtestaddedvaluefromreadmodelPreIndexData</body>\n"
            . "</html>",
            $actualOutput
        );
    }

    public function testRunMethodWithAllEventsWithMergedControllerEventOutputFromAggregateRootControllerWithArrayListeners()
    {
        // Redirect output to command output
        $this->setOutputCallback(function () {
        });

        ob_start();

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/fakeaggregatearraylisteners/index']);
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

        $eventLogRepositoryMock = \Mockery::mock('overload:' . EventLogRepository::class);
        $eventLogRepositoryMock
            ->shouldReceive('commit')
            ->once();

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $baseConfig['doctrine']['ORM']['dem1'] = [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'user'     => 'fwuser',
            'password' => 'testpass',
            'dbname'   => 'fw',
        ];

        $baseConfig['events'] = [
            // PSR-14 compliant Event Bus.
            'psr14_event_dispatcher' => \Ascmvc\EventSourcing\EventDispatcher::class,
            // Different read and write connections allow for simplified (!) CQRS. :)
            'read_conn_name' => 'dem1',
            'write_conn_name' => 'dem1',
        ];

        $baseConfig['eventlog'] = [
            'enabled' => true,
            'doctrine' => [
                'log_conn_name' => 'dem1',
                'entity_name' => \Application\Log\Entity\EventLog::class,
            ],
            // Leave empty to log everything, including the kitchen sink. :)
            // If you you start whitelisting events, it will blacklist everything else by default.
            'log_event_type' => [
                'whitelist' => [
                    \Ascmvc\EventSourcing\Event\WriteAggregateCompletedEvent::class,
                ],
                'blacklist' => [
                    //\Ascmvc\EventSourcing\Event\AggregateEvent::class,
                ],
            ],
        ];

        $baseConfig['routes'] = [
            0 => [
                'GET',
                '/fakeaggregatearraylisteners[/{action}]',
                'fakeaggregatearraylisteners',
            ],
        ];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $ascmvcEvent = $app->getEvent();
        $ascmvcEvent->setName(AscmvcEvent::EVENT_ROUTE);
        $ascmvcEvent->setApplication($app);
        $app->setEvent($ascmvcEvent);

        $app->run();

        $actualOutput = ob_get_contents();

        $this->assertSame(
            "<html>\n"
            . "<head>\n"
            . "</head>\n"
            . "<body>\n"
            . "<!-- Plates template -->\n"
            . "FakeaggregatearraylistenersControllerSTDOUTtestaddedvaluefromarraylistenersreadmodelfromreadmodelPreIndexData</body>\n"
            . "</html>",
            $actualOutput
        );
    }

    public function testSessionIsEnabled()
    {
        // Redirect output to command output
        $this->setOutputCallback(function () {
        });

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/test1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $baseConfig['session'] = [
            'enabled' => true,
            'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
            'doctrine_cache_driver' => \Doctrine\Common\Cache\FilesystemCache::class,
            //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
            //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
            //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
            //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
            'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
            'doctrine_cache_server_params' => [
                'host' => '127.0.0.1',
                'port' => 6379, // redis
                //'port' => 11211 // memcached/memcache
            ],
            'session_name' => 'PHPSESSION',
            'session_path' => '/',
            'session_domain' => 'localdomain.local',
            'session_secure' => false,
            'session_httponly' => false,
            'session_id_length' => 32,
            'session_id_type' => 1,
            'session_storage_prefix' => 'ascmvc',
            'session_token_regeneration' => 60 * 30, // 30 minutes
            'session_expire' => 60 * 60, // 60 minutes
        ];

        $app = App::getInstance();

        $config = new \Ascmvc\Session\Config($baseConfig['session']);
        $sessionManager = \Ascmvc\Session\SessionManager::getSessionManager(null, null, $config, true);

        try {
            $sessionManager->start();
        } catch (\Throwable $exception) {
            var_dump($exception);
        }

        $app->setSessionManager($sessionManager);

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $this->assertInstanceOf(SessionManager::class, $app->getSessionManager());

        $this->assertTrue($app->getSessionManager()->isEnabled());
    }

    public function testSessionIsNotEnabled()
    {
        // Redirect output to command output
        $this->setOutputCallback(function () {
        });

        if (!defined('BASEDIR')) {
            define('BASEDIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app');
        }

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
            ->andReturn(['REQUEST_URI' => '/test1']);
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

        $baseConfig['BASEDIR'] = BASEDIR;

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

        $baseConfig['session'] = [
            'enabled' => false,
            'psr6_cache_pool' => \Ascmvc\Session\Cache\DoctrineCacheItemPool::class,
            'doctrine_cache_driver' => \Doctrine\Common\Cache\FilesystemCache::class,
            //'doctrine_cache_driver' => \Doctrine\Common\Cache\XcacheCache::class,
            //'doctrine_cache_driver' => \Doctrine\Common\Cache\RedisCache::class,
            //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcachedCache::class,
            //'doctrine_cache_driver' => \Doctrine\Common\Cache\MemcacheCache::class,
            'doctrine_filesystem_cache_directory' => BASEDIR . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR,
            'doctrine_cache_server_params' => [
                'host' => '127.0.0.1',
                'port' => 6379, // redis
                //'port' => 11211 // memcached/memcache
            ],
            'session_name' => 'PHPSESSION',
            'session_path' => '/',
            'session_domain' => 'localdomain.local',
            'session_secure' => false,
            'session_httponly' => false,
            'session_id_length' => 32,
            'session_id_type' => 1,
            'session_storage_prefix' => 'ascmvc',
            'session_token_regeneration' => 60 * 30, // 30 minutes
            'session_expire' => 60 * 60, // 60 minutes
        ];

        $app = App::getInstance();

        $config = new \Ascmvc\Session\Config($baseConfig['session']);
        $sessionManager = \Ascmvc\Session\SessionManager::getSessionManager(null, null, $config, true);

        try {
            $sessionManager->start();
        } catch (\Throwable $exception) {
            var_dump($exception);
        }

        $app->setSessionManager($sessionManager);

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->setRequest($requestMock);

        $this->assertInstanceOf(SessionManager::class, $app->getSessionManager());

        $this->assertFalse($app->getSessionManager()->isEnabled());
    }
}
