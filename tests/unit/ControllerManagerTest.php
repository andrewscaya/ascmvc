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

use \Application\Controllers\FakeController;
use Application\Controllers\FakefactoryController;
use Ascmvc\Mvc\App;
use Ascmvc\Mvc\ControllerManager;
use PHPUnit\Framework\TestCase;
use \Specialmodule\Controllers\FakeController as FakeController2;
use Zend\Diactoros\ServerRequestFactory;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ControllerManagerTest extends TestCase
{
    public function tearDown()
    {
        \Mockery::close();

        parent::tearDown();
    }

    public function testControllerManagerWillReturnAppropriateControllerWithDefaultIndexAction()
    {
        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $controllerManager = new ControllerManager($app, 'fake', ['get' => ['test' => 'fakeparams1']]);

        $this->assertInstanceOf(FakeController::class, $app->getController());

        $controllerOutput = $controllerManager->execute();

        $this->assertSame(
            'ControllerManagerTest_testControllerManagerWillReturnAppropriateControllerWithDefaultIndexAction_fakeparams1',
            $controllerOutput
        );
    }

    public function testControllerManagerWillReturnAppropriateControllerWithSpecificAction()
    {
        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $vars = [
            'get' => [
                'action' => 'special',
                'test' => 'fakeparams2',
            ],
        ];

        $controllerManager = new ControllerManager($app, 'fake', $vars);

        $this->assertInstanceOf(FakeController::class, $app->getController());

        $controllerOutput = $controllerManager->execute();

        $this->assertSame(
            'ControllerManagerTest_testControllerManagerWillReturnAppropriateControllerWithSpecificAction_fakeparams2',
            $controllerOutput
        );
    }

    public function testControllerManagerWillReturnAppropriateControllerWithDefaultIndexActionAndModuleName()
    {
        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $vars = [
            'get' => [
                'test' => 'fakeparams3',
            ],
        ];

        $controllerManager = new ControllerManager($app, 'specialmodule/fake', $vars);

        $this->assertInstanceOf(FakeController2::class, $app->getController());

        $controllerOutput = $controllerManager->execute();

        $this->assertSame(
            'ControllerManagerTest_testControllerManagerWillReturnAppropriateControllerWithDefaultIndexActionAndModuleName_fakeparams3',
            $controllerOutput
        );
    }

    public function testControllerManagerWillReturnAppropriateControllerWithSpecificActionAndModuleName()
    {
        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $vars = [
            'get' => [
                'action' => 'special',
                'test' => 'fakeparams4',
            ],
        ];

        $controllerManager = new ControllerManager($app, 'specialmodule/fake', $vars);

        $this->assertInstanceOf(FakeController2::class, $app->getController());

        $controllerOutput = $controllerManager->execute();

        $this->assertSame(
            'ControllerManagerTest_testControllerManagerWillReturnAppropriateControllerWithSpecificActionAndModuleName_fakeparams4',
            $controllerOutput
        );
    }

    public function testControllerManagerWillReturnAppropriateRuntimeExceptionWhenControllerIsNotFound()
    {
        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $vars = [
            'get' => [
                'action' => 'special',
                'test' => 'fakeparams5',
            ],
        ];

        $this->expectException(\RuntimeException::class);

        $controllerManager = new ControllerManager($app, 'specialmodule/faker', $vars);
    }

    public function testControllerManagerWillReturnAppropriateRuntimeExceptionWhenMethodIsNotFound()
    {
        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $vars = [
            'get' => [
                'action' => 'morespecial',
                'test' => 'fakeparams6',
            ],
        ];

        $this->expectException(\RuntimeException::class);

        $controllerManager = new ControllerManager($app, 'specialmodule/fake', $vars);
    }

    public function testControllerManagerWillReturnAppropriateControllerWithDefaultIndexActionAndFactoryInterface()
    {
        $serverRequestFactoryMock = \Mockery::mock('alias:' . ServerRequestFactory::class);
        $serverRequestFactoryMock
            ->shouldReceive('fromGlobals')
            ->once();

        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

        $baseConfig['templateManager'] = 'Plates';
        $baseConfig['templates']['templateDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates';
        $baseConfig['templates']['compileDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'templates_c';
        $baseConfig['templates']['configDir'] =
            dirname(__FILE__)
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'config';

        $baseConfig['env'] = 'development';

        $baseConfig['view'] = [];

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $controllerManager = new ControllerManager($app, 'fakefactory');

        $this->assertInstanceOf(FakefactoryController::class, $app->getController());

        $controllerOutput = $controllerManager->execute();

        $this->assertSame(
            'This is the test message',
            $controllerOutput
        );
    }
}
