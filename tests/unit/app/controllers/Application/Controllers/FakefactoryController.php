<?php

namespace Application\Controllers;

use Ascmvc\AscmvcControllerFactoryInterface;
use Ascmvc\EventSourcing\EventDispatcher;
use Ascmvc\Mvc\Controller;
use Pimple\Container;

class FakefactoryController extends Controller implements AscmvcControllerFactoryInterface
{
    protected $testMessage;

    public function setTestMessage(string $testMessage)
    {
        $this->testMessage = $testMessage;
    }

    public static function factory(array &$baseConfig, EventDispatcher &$eventDispatcher, Container &$serviceManager, &$viewObject)
    {
        $serviceManager[FakefactoryController::class] = $serviceManager->factory(function ($serviceManager) use ($baseConfig, $eventDispatcher) {
            $controller = new FakefactoryController($baseConfig, $eventDispatcher);
            $controller->setTestMessage('This is the test message');
            return $controller;
        });
    }

    public function indexAction($vars = null)
    {
        return $this->testMessage;
    }
}