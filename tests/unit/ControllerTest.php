<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.2
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace AscmvcTest;

use Application\Controllers\FakeController;
use Ascmvc\EventSourcing\EventProcessor;
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

        $baseConfig['view'] = [
            'title' => "Skeleton Application",
            'author' => 'Andrew Caya',
            'description' => 'Small CRUD application',
        ];

        $eventProcessor = new EventProcessor();

        $controller = new FakeController($baseConfig, $eventProcessor);

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
