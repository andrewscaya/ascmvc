<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    4.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.1.0
 */

namespace Ascmvc\EventSourcing;

use Ascmvc\AbstractApp;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Process\Process;

/**
 * Class CommandRunner
 *
 * @package Ascmvc\EventSourcing
 */
class CommandRunner
{
    /**
     * Indicates if the application is running inside a Swoole coroutine or not.
     *
     * @var bool
     */
    protected $swoole = false;

    /**
     * Contains an instance of the AbstractApp class.
     *
     * @var AbstractApp
     */
    protected $application;

    /**
     * Name of the command to run.
     *
     * @var string
     */
    protected $name;

    /**
     * Contains the command's arguments.
     *
     * @var array
     */
    protected $arguments;

    /**
     * Contains an instance of the class that will process the command.
     *
     * @var mixed
     */
    protected $commandProcess;

    /**
     * Contains the command's standard output (STDOUT).
     *
     * @var string
     */
    protected $output;

    /**
     * Contains the command's standard error (STDERR).
     *
     * @var string
     */
    protected $error;

    /**
     * CommandRunner constructor.
     *
     * @param AbstractApp $application
     * @param string $name
     * @param array $arguments
     * @param bool $swoole
     */
    public function __construct(AbstractApp $application, string $name, array $arguments = [], bool $swoole = false)
    {
        $this->application = $application;

        $this->name = $name;

        $this->arguments = $arguments;

        $this->swoole = $swoole;
    }

    /**
     * Runs the command.
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function start()
    {
        if (is_null($this->commandProcess)) {
            $baseConfig = $this->application->getBaseConfig();

            // @codeCoverageIgnoreStart
            if ($this->swoole) {
                $cmdapp = new Application();

                foreach ($baseConfig['async_commands'] as $asyncCommandName) {
                    $asyncCommand = new $asyncCommandName($this->application);

                    $cmdapp->add($asyncCommand);
                }

                $this->commandProcess = $cmdapp->find($this->name);

                $commandInput = new ArrayInput($this->arguments);

                $commandOutput = new BufferedOutput();

                $this->commandProcess->run($commandInput, $commandOutput);

                $this->output = $commandOutput->fetch();
            } else {
                // @codeCoverageIgnoreEnd
                $asyncBus = $this->application->getBaseConfig()['async_process_bin'];

                $arguments = '';

                if (isset($this->arguments['execute'])) {
                    $execute = $this->arguments['execute'];

                    unset($this->arguments['execute']);

                    $arguments .= $execute . ' ';
                }

                if (isset($this->arguments['--values'])) {
                    $values = $this->arguments['--values'];

                    $arguments .= '--values ' . escapeshellarg($values) . ' ';
                }

                if (!empty($arguments)) {
                    $commandline = 'php '
                        . $asyncBus
                        . ' '
                        . $this->name
                        . ' '
                        . $arguments;
                } else {
                    $commandline = 'php '
                        . $asyncBus
                        . ' '
                        . $this->name;
                }

                $this->commandProcess = new Process($commandline);

                $this->commandProcess->setTty($this->commandProcess->isTtySupported());

                $this->commandProcess->setTimeout(null);

                // @codeCoverageIgnoreStart
                try {
                    $this->commandProcess->mustRun();
                } catch (ProcessFailedException $e) {
                    $this->error = $e->getMessage();
                    throw new \Exception($commandline . ' failed');
                }
                // @codeCoverageIgnoreEnd

                // Can be used for callback architecture style.
                //$this->commandProcess->start();
                //$this->commandProcess->wait();
            }
        }

        if (!isset($this->output)) {
            // @codeCoverageIgnoreStart
            while ($this->commandProcess->isRunning()) {
                return true;
            }
            // @codeCoverageIgnoreEnd

            $this->output = $this->commandProcess->getOutput();
        }

        return false;
    }

    // @codeCoverageIgnoreStart
    /**
     * Gets the command's standard output (STDOUT).
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Gets the command's standard error (STDERR).
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
    // @codeCoverageIgnoreEnd
}
