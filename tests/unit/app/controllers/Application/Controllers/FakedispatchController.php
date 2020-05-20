<?php

namespace Application\Controllers;

use Ascmvc\Mvc\AscmvcEvent;
use Ascmvc\Mvc\Controller;
use Laminas\Diactoros\Response;

class FakedispatchController extends Controller
{
    public function onDispatch(AscmvcEvent $event)
    {
        $response = new Response();
        $response->getBody()->write('FakedispatchController onDispatch');
        return $response;
    }

    public function onRender(AscmvcEvent $event)
    {
        $response = new Response();
        $response->getBody()->write('FakedispatchController onRender');
        return $response;
    }

    public function indexAction($vars = null)
    {
        return 'AppTest_testRunMethodWithOnDispatchShortCircuit_IndexAction_' . $vars['get']['test'];
    }

    public function specialAction($vars = null)
    {
        return 'AppTest_testRunMethodWithOnDispatchShortCircuit_SpecialAction_' . $vars['get']['test'];
    }
}