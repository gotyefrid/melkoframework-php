<?php

namespace core;

class Application
{
    public static $appPath = __DIR__;
    public static AppConfig $app;

    public function __construct()
    {
        self::$app = new AppConfig(
            new Router(),
            new ErrorHandler(),
            new Request(),
        );
    }

    /**
     * @return void
     */
    public function run(): void
    {
        try {
            echo self::$app->router->resolve();
        } catch (\Throwable $e) {
            echo Application::$app->errorHandler->handle($e);
        }
    }
}