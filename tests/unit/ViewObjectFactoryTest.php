<?php

/**
 * LightMVC/ASCMVC
 *
 * Copyright 2010 - 2019 Foreach Code Factory <ascmvc@asclinux.net>
 * Version 2.0.1
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

use Ascmvc\Mvc\ViewObjectFactory;
use League\Plates\Engine;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ViewObjectFactoryTest extends TestCase
{
    public function testGetPlatesInstanceWithDevelopmentEnvironment()
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

        $viewObject = ViewObjectFactory::getInstance($baseConfig);

        $this->assertInstanceOf(Engine::class, $viewObject);
    }

    public function testGetTwigInstanceWithDevelopmentEnvironment()
    {
        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

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

        $viewObject = ViewObjectFactory::getInstance($baseConfig);

        $this->assertInstanceOf(\Twig_Environment::class, $viewObject);
    }

    public function testGetSmartyInstanceWithDevelopmentEnvironment()
    {
        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

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

        $viewObject = ViewObjectFactory::getInstance($baseConfig);

        $this->assertInstanceOf(\Smarty::class, $viewObject);
    }

    public function testGetPlatesInstanceWithProductionEnvironment()
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
        $baseConfig['templates']['cacheDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'cache';

        $baseConfig['env'] = 'production';

        $viewObject = ViewObjectFactory::getInstance($baseConfig);

        $this->assertInstanceOf(Engine::class, $viewObject);
    }

    public function testGetTwigInstanceWithProductionEnvironment()
    {
        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

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
        $baseConfig['templates']['cacheDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'cache';

        $baseConfig['env'] = 'production';

        $viewObject = ViewObjectFactory::getInstance($baseConfig);

        $this->assertInstanceOf(\Twig_Environment::class, $viewObject);
    }

    public function testGetSmartyInstanceWithProductionEnvironment()
    {
        $baseConfig['BASEDIR'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app';

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
        $baseConfig['templates']['cacheDir'] =
            $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'cache';

        $baseConfig['env'] = 'production';

        $viewObject = ViewObjectFactory::getInstance($baseConfig);

        $this->assertInstanceOf(\Smarty::class, $viewObject);
    }
}
