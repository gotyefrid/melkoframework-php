<?php
declare(strict_types=1);

namespace core;

use PDO;
use Throwable;

class App
{
    public static App $app;
    public bool $isGetParamRouter;
    private Request $request;
    private PDO $pdo;
    private ErrorHandler $errorHandler;


    public function __construct(
        Request $request,
        PDO $pdo,
        ErrorHandler $errorHandler,
        bool $isGetParamRouter = false
    )
    {
        $this->request = $request;
        $this->pdo = $pdo;
        $this->errorHandler = $errorHandler;
        $this->isGetParamRouter = $isGetParamRouter;
        self::$app = $this;
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function run(): void
    {
        try {
            echo $this->getRequest()->resolve();
        } catch (Throwable $e) {
            echo self::$app->getErrorHandler()->handle($e);
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