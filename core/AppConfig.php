<?php

namespace core;


class AppConfig
{
    /**
     * @var Router
     */
    public $router;

    /**
     * @var ErrorHandler
     */
    public $errorHandler;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var \PDO
     */
    public $db;

    public function __construct(
        Router $router,
        ErrorHandler $errorHandler,
        Request $request,
        \PDO $db
    )
    {
        $this->router = $router;
        $this->errorHandler = $errorHandler;
        $this->request = $request;
        $this->db = $db;
    }
}