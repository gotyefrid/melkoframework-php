<?php

namespace core;

class Application
{
    public static $appPath = __DIR__;

    /**
     * @var Router
     */
    public $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    /**
     * @return void
     */
    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Throwable $e) {
            echo (new ErrorHandler($e))->handle();
        }
    }
}