<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.1.2
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.1.0
 */

namespace Ascmvc\EventSourcing;

use Ascmvc\AbstractApp;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AsyncCommand
 *
 * @package Ascmvc\EventSourcing
 */
class AsyncCommand extends Command
{
    /**
     * Contains an instance of the AbstractApp class.
     *
     * @var AbstractApp
     */
    protected $webapp;

    /**
     * Name of the command.
     *
     * @var string
     */
    protected static $defaultName;

    /**
     * AsyncCommand constructor.
     *
     * @param AbstractApp $webapp
     */
    public function __construct(AbstractApp $webapp)
    {
        // you *must* call the parent constructor
        parent::__construct();

        $this->webapp = $webapp;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        throw new LogicException('You must override the execute() method in the concrete command class.');
    }

    /**
     * Gets the instance of the AbstractApp class.
     *
     * @return AbstractApp
     */
    protected function getWebapp(): AbstractApp
    {
        return $this->webapp;
    }
}
