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

namespace AscmvcTest;

use Ascmvc\Mvc\App;
use Ascmvc\Mvc\AscmvcEvent;
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
            define('BASEDIR', dirname(__FILE__));
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

        $baseConfig['BASEDIR'] = BASEDIR . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $app->appendBaseConfig('test', ['test1']);

        $this->assertArrayHasKey('test', $app->getBaseConfig());
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
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $app = App::getInstance();

        $baseConfig = $app->boot();

        $this->assertFalse(URLBASEADDR);

        $this->assertSame(
            [
                'BASEDIR' => '/srv/ascmvc/tests/unit/app',
                'URLBASEADDR' => false,
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
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

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

        $app = App::getInstance();

        $baseConfig = $app->boot();

        $this->assertFalse(URLBASEADDR);

        $this->assertSame(
            [
            'BASEDIR' => '/srv/ascmvc/tests/unit/app',
            'URLBASEADDR' => false,
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

    public function testBootMethodLoadsConfigurationFilesWithDoctrine()
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
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

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

    public function testBootMethodLoadsConfigurationFilesWithMiddleware()
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
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

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

    public function testBootMethodLoadsConfigurationFilesWithMiddlewareEmptyPipe()
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
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

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
}
