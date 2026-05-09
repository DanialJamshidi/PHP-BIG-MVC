<?php

namespace app\routes;

class Web
{
    public function routes()
    {
        Route::Get("/", "HomeController", "index", "HomeMiddleware");
        return Route::$routes;
    }
}
