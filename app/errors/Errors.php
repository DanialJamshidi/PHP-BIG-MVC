<?php

namespace app\errors;

class Errors
{
    static public function _403_()
    {
        http_response_code(403);
        exit;
    }
    static public function _404_()
    {
        http_response_code(404);
        exit;
    }
    static public function _500_()
    {
        http_response_code(500);
        exit;
    }
}
