<?php

namespace Application\Controllers;

use Ascmvc\Mvc\Controller;


class C405Controller extends Controller
{

    public function indexAction($vars = null)
    {
        $this->view['vars'] = $vars;

        $this->view['bodyjs'] = 1;

        $this->view['templatefile'] = 'c405_index';

        $this->view['statuscode'] = 405;

        return $this->view;
    }

}
