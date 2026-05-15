<?php

namespace app\controllers;
use app\core\libraries\Controller;

class ExampleController extends Controller
{
    public function index()
    {
        return Controller::view("welcome");
    }
}