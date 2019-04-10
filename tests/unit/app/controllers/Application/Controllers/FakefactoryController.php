<?php

namespace Application\Controllers;

use Ascmvc\AscmvcControllerFactoryInterface;
use Ascmvc\EventSourcing\EventProcessor;
use Ascmvc\Mvc\Controller;
use Pimple\Container;

class FakefactoryController extends Controller implements AscmvcControllerFactoryInterface
{
    protected $testMessage;

    public function setTestMessage(string $testMessage)
    {
        $this->testMessage = $testMessage;
    }

    public static function factory(array &$baseConfig, EventProcessor &$eventProcessor, Container &$serviceManager, &$viewObject)
    {
        $serviceManager[FakefactoryController::class] = $serviceManager->factory(function ($serviceManager) use ($baseConfig, $eventProcessor) {
            $controller = new FakefactoryController($baseConfig, $eventProcessor);
            $controller->setTestMessage('This is the test message');
            return $controller;
        });
    }

    public function indexAction($vars = null)
    {
        return $this->testMessage;
    }
}