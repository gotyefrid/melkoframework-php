<?php
declare(strict_types=1);

namespace Gotyefrid\MelkoframeworkCore;

use DomainException;
use Exception;
use PDO;
use Throwable;

class App
{
    public bool $isGetParamRouter;
    private AbstractRequest $request;
    private PDO $pdo;
    private AbstractErrorHandler $errorHandler;
    private static ?self $instance = null;


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
        self::$instance = $this;
    }

    public static function get(): self
    {
        if (!self::$instance) {
            throw new DomainException('App instance does not exist');
        }

        return self::$instance;
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
            echo $this->getErrorHandler()->handle($e);
        }
    }

    public function getRequest(): AbstractRequest
    {
        return $this->request;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getErrorHandler(): AbstractErrorHandler
    {
        return $this->errorHandler;
    }
}