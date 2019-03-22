<?php

namespace Application\Controllers;

use Ascmvc\AscmvcControllerFactoryInterface;
use Ascmvc\Mvc\AscmvcEventManager;
use Ascmvc\Mvc\Controller;
use Pimple\Container;

class FakefactoryController extends Controller implements AscmvcControllerFactoryInterface
{
    protected $testMessage;

    public function setTestMessage(string $testMessage)
    {
        $this->testMessage = $testMessage;
    }

    public static function factory(array &$baseConfig, &$viewObject, Container &$serviceManager, AscmvcEventManager &$eventManager)
    {
        $serviceManager[FakefactoryController::class] = $serviceManager->factory(function ($serviceManager) use ($baseConfig) {
            $controller = new FakefactoryController($baseConfig);
            $controller->setTestMessage('This is the test message');
            return $controller;
        });
    }

    public function indexAction($vars = null)
    {
        return $this->testMessage;
    }
}