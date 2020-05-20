<?php

namespace Specialmodule\Controllers;

use Ascmvc\Mvc\Controller;
use Laminas\Diactoros\Response;

class FakeController extends Controller
{
    public function indexAction($vars = null)
    {
        return 'ControllerManagerTest_testControllerManagerWillReturnAppropriateControllerWithDefaultIndexActionAndModuleName_' . $vars['get']['test'];
    }

    public function specialAction($vars = null)
    {
        return 'ControllerManagerTest_testControllerManagerWillReturnAppropriateControllerWithSpecificActionAndModuleName_' . $vars['get']['test'];
    }

    public function eventmanagertestarrayAction($vars = null)
    {
        $this->view['results'] = 'AscmvcEventManagerTest_testDefaultMVCEventsCanBeTriggeredWithArrayAsControllerOutput_' . $vars['get']['test'];

        $this->view['templatefile'] = 'test_index';

        return $this->view;
    }

    public function eventmanagertestresponseAction($vars = null)
    {
        $response = new Response();
        $response
            ->getBody()
            ->write(
                'AscmvcEventManagerTest_testDefaultMVCEventsCanBeTriggeredWithResponseAsControllerOutput_' . $vars['get']['test']
            );

        return $response;
    }

    public function eventmanagerteststringAction($vars = null)
    {
        return 'AscmvcEventManagerTest_testDefaultMVCEventsCanBeTriggeredWithStringAsControllerOutput_' . $vars['get']['test'];
    }
}