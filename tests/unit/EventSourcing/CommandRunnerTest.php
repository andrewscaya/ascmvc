<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.1.1
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      3.1.0
 */

namespace AscmvcTest;

use Ascmvc\EventSourcing\CommandRunner;
use Ascmvc\Mvc\App;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CommandRunnerTest extends TestCase
{
    public function testCommandRunnerConstructor()
    {
        if (!defined('BASEDIR2')) {
            define('BASEDIR2', dirname(dirname(__FILE__))
                . DIRECTORY_SEPARATOR
                . 'app');
        }

        $processMock = \Mockery::mock('overload:' . Process::class);
        $processMock
            ->shouldReceive('isTtySupported')
            ->once()
            ->andReturn(true);
        $processMock
            ->shouldReceive('setTty')
            ->with(true)
            ->once();
        $processMock
            ->shouldReceive('setTimeout')
            ->with(null)
            ->once();
        $processMock
            ->shouldReceive('mustRun')
            ->once();
        $processMock
            ->shouldReceive('isRunning')
            ->once()
            ->andReturn(false);
        $processMock
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('This is a test');

        $baseConfig['BASEDIR'] = BASEDIR2;

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

        $baseConfig['async_process_bin'] = $baseConfig['BASEDIR']
            . DIRECTORY_SEPARATOR
            . 'bin'
            . DIRECTORY_SEPARATOR
            . 'process.php';

        $app = App::getInstance();

        // Deliberately not calling the app's boot() method
        $app->initialize($baseConfig);

        $arguments = [
            'execute' => 'testcommand',
            '--values' => serialize(['id' => 4])
        ];

        $commandRunner1 = new CommandRunner($app, 'test:run', $arguments, false);

        $this->assertInstanceOf(CommandRunner::class, $commandRunner1);

        $commandRunner1->start();

        $this->assertSame('This is a test', $commandRunner1->getOutput());

        $arguments = [];

        $commandRunner2 = new CommandRunner($app, 'test:run', $arguments, false);

        $this->assertInstanceOf(CommandRunner::class, $commandRunner2);

        $commandRunner2->start();

        $this->assertSame('This is a test', $commandRunner2->getOutput());
    }
}
