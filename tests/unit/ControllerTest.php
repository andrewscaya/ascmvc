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

namespace AscmvcTest;

use Application\Controllers\FakeController;
use Ascmvc\EventSourcing\EventDispatcher;
use Ascmvc\Mvc\App;
use Ascmvc\Mvc\AscmvcEvent;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ControllerTest extends TestCase
{
    public function testControllerWillStoreConfigurationAndViewSettings()
    {
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

        $baseConfig['view'] = [
            'title' => "Skeleton Application",
            'author' => 'Andrew Caya',
            'description' => 'Small CRUD application',
        ];

        $ascmvcEvent = new AscmvcEvent(AscmvcEvent::EVENT_DISPATCH);

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $eventDispatcher = new EventDispatcher($app);

        $controller = new FakeController($baseConfig, $eventDispatcher);

        $controllerReflection = new \ReflectionClass($controller);

        $propertiesList = $controllerReflection->getProperties();

        for ($i = 0; $i < count($propertiesList); $i++) {
            $key = $propertiesList[$i]->name;
            $controllerProperties[$key] = $propertiesList[$i];
            $controllerProperties[$key]->setAccessible(true);
        }

        $this->assertArrayHasKey('view', $controllerProperties);
        $this->assertSame('Skeleton Application', $controllerProperties['view']->getValue($controller)['title']);
        $this->assertSame('Andrew Caya', $controllerProperties['view']->getValue($controller)['author']);
        $this->assertSame('Small CRUD application', $controllerProperties['view']->getValue($controller)['description']);

        $this->assertArrayHasKey('baseConfig', $controllerProperties);
        $this->assertSame(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app',
            $controllerProperties['baseConfig']->getValue($controller)['BASEDIR']
        );
    }
}
