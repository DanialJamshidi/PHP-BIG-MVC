<?php

namespace app\libraries;

use app\configs\Config;
use app\errors\Errors;

class Controller
{
    static public function view($view, $data = [])
    {
        $path = Config::APPROOT() . "/resources/views/" . periodPath($view) . ".php";
        extract($data, EXTR_SKIP);
        if (file_exists($path)) {
            require_once $path;
        } else {
            Errors::_404_();
        }
    }
}
