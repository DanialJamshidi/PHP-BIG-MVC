<?php
namespace app\middlewares;

class HomeMiddleware
{
    public function handle()
    {
        dd("Middleware");
        return true;
    }
}