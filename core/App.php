<?php
declare(strict_types=1);

namespace core;

use PDO;
use Throwable;

class App
{
    public bool $isGetParamRouter;
    private Request $request;
    private PDO $pdo;
    private ErrorHandler $errorHandler;


    public function __construct(
        AbstractRequest $request,
        PDO $pdo,
        AbstractErrorHandler $errorHandler,
        bool $isGetParamRouter = false
    )
    {
        $this->request = $request;
        $this->pdo = $pdo;
        $this->errorHandler = $errorHandler;
        $this->isGetParamRouter = $isGetParamRouter;
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function run(): void
    {
        try {
            echo $this->getRequest()->resolve();
            exit();
        } catch (Throwable $e) {
            echo $this->getErrorHandler()->handle($e);
            exit();
        }
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getErrorHandler(): ErrorHandler
    {
        return $this->errorHandler;
    }
}