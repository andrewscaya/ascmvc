<?php

namespace Application\Controllers;

use Ascmvc\Mvc\Controller;

class FakestandardController extends Controller
{
    public function indexAction($vars = null)
    {
        return 'AppTest_testControllerManagerWillReturnAppropriateControllerWithDefaultIndexAction_' . $vars['get']['test'];
    }

    public function specialAction($vars = null)
    {
        return 'AppTest_testControllerManagerWillReturnAppropriateControllerWithSpecificAction_' . $vars['get']['test'];
    }
}