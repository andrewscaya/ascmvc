<?php

namespace Application\Controllers;

use Ascmvc\Mvc\AscmvcEvent;
use Ascmvc\Mvc\Controller;
use Zend\Diactoros\Response;

class FakeController extends Controller
{
    public static function onBootstrap(AscmvcEvent $event)
    {
        $response = new Response();
        $response->getBody()->write('FakeController onBootstrap');
        return $response;
    }

    public function onDispatch(AscmvcEvent $event)
    {
        $response = new Response();
        $response->getBody()->write('FakeController onDispatch');
        return $response;
    }

    public function onRender(AscmvcEvent $event)
    {
        $response = new Response();
        $response->getBody()->write('FakeController onRender');
        return $response;
    }

    public function onFinish(AscmvcEvent $event)
    {
        $response = new Response();
        $response->getBody()->write('FakeController onFinish');
        $event->getApplication()->setResponse($response);
    }

    public function indexAction($vars = null)
    {
        return 'ControllerManagerTest_testControllerManagerWillReturnAppropriateControllerWithDefaultIndexAction_' . $vars['get']['test'];
    }

    public function specialAction($vars = null)
    {
        return 'ControllerManagerTest_testControllerManagerWillReturnAppropriateControllerWithSpecificAction_' . $vars['get']['test'];
    }
}