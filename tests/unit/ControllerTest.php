<?php
/**
 * LightMVC/ASCMVC
 *
 * Copyright 2010 - 2019 Foreach Code Factory <ascmvc@asclinux.net>
 * Version 2.0.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package    lightmvc/ascmvc
 * @copyright  Copyright 2010 - 2019 Foreach Code Factory <ascmvc@asclinux.net>
 * @link       http://lightmvc.net/
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since 2.0.0
 */

namespace AscmvcTest;

use Application\Controllers\FakeController;
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

        $controller = new FakeController($baseConfig);

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
