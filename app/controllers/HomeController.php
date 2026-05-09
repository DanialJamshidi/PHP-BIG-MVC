<?php

namespace app\controllers;

use app\libraries\Controller;
use app\models\Mvc;

class HomeController extends Controller
{
    public function index()
    {
        $mvc = Mvc::getAll();
        return Controller::view("home.index", compact("mvc"));
    }
}