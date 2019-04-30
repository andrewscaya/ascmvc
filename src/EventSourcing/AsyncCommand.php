<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.1.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.1.0
 */

namespace Ascmvc\EventSourcing;

use Ascmvc\AbstractApp;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AsyncCommand extends Command
{
    /**
     * Contains an instance of the AbstractApp class.
     *
     * @var AbstractApp
     */
    protected $webapp;

    protected static $defaultName;

    public function __construct(AbstractApp $webapp)
    {
        // you *must* call the parent constructor
        parent::__construct();

        $this->webapp = $webapp;
    }

    protected function configure()
    {
    }

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
