<?php

namespace core;

class AppConfig
{
    public Router $router;
    public ErrorHandler $errorHandler;
    public Request $request;

    public function __construct(
        Router $router,
        ErrorHandler $errorHandler,
        Request $request
    )
    {
        $this->router = $router;
        $this->errorHandler = $errorHandler;
        $this->request = $request;
    }
}