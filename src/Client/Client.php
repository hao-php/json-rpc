<?php

namespace Haoa\JsonRpc\Client;

use Mix\ObjectPool\Exception\WaitTimeoutException;
use Haoa\JsonRpc\Client\Pool\ConnectionPool;
use Haoa\JsonRpc\Client\Pool\Dialer;

class Client implements ConnectionInterface
{


    /**
     * 主机
     * @var string
     */
    protected $host = '';

    /**
     * 端口
     * @var int
     */
    protected $port = 0;

    /**
     * swoole socket type
     */
    protected $socketType;

    /**
     * 全局超时
     * @var float
     */
    protected $timeout = 5.0;


    /**
     * 最大活跃数
     * "0" 为不限制，"-1" 等于cpu数量
     * @var int
     */
    protected $maxOpen = -1;

    /**
     * 最多可空闲连接数
     * "-1" 等于cpu数量
     * @var int
     */
    protected $maxIdle = -1;

    /**
     * 连接可复用的最长时间
     * "0" 为不限制
     * @var int
     */
    protected $maxLifetime = 0;

    /**
     * 等待新连接超时时间
     * "0" 为不限制
     * @var float
     */
    protected $waitTimeout = 0.0;

    /**
     * 连接池
     * @var ConnectionPool
     */
    protected $pool;

    /**
     * @var Driver
     */
    protected $driver;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param string $host
     * @param int $port
     * @param $socketType int SWOOLE_SOCK_TCP SWOOLE_SOCK_UNIX_STREAM
     * @param float $timeout
     */
    public function __construct(string $host, int $port, $socketType = SWOOLE_SOCK_TCP, float $timeout = 5.0)
    {
        $this->host = $host;
        $this->port = $port;
        $this->socketType = $socketType;
        $this->timeout = $timeout;

        $this->driver = new Driver(
            $this->host,
            $this->port,
            $this->socketType,
            $this->timeout,
        );
    }

    protected function createPool()
    {
        if ($this->driver) {
            $this->driver->close();
            $this->driver = null;
        }

        $this->pool = new ConnectionPool(
            new Dialer(
                $this->host,
                $this->port,
                $this->socketType,
                $this->timeout,
            ),
            $this->maxOpen,
            $this->maxIdle,
            $this->maxLifetime,
            $this->waitTimeout
        );
    }

    /**
     * @param int $maxOpen
     * @param int $maxIdle
     * @param int $maxLifetime
     * @param float $waitTimeout
     */
    public function startPool(int $maxOpen, int $maxIdle, int $maxLifetime = 0, float $waitTimeout = 0.0)
    {
        $this->maxOpen = $maxOpen;
        $this->maxIdle = $maxIdle;
        $this->maxLifetime = $maxLifetime;
        $this->waitTimeout = $waitTimeout;
        $this->createPool();
    }

    /**
     * @param int $maxOpen
     */
    public function setMaxOpenConns(int $maxOpen)
    {
        if ($this->maxOpen == $maxOpen) {
            return;
        }
        $this->maxOpen = $maxOpen;
        $this->createPool();
    }

    /**
     * @param int $maxIdle
     */
    public function setMaxIdleConns(int $maxIdle)
    {
        if ($this->maxIdle == $maxIdle) {
            return;
        }
        $this->maxIdle = $maxIdle;
        $this->createPool();
    }

    /**
     * @param int $maxLifetime
     */
    public function setConnMaxLifetime(int $maxLifetime)
    {
        if ($this->maxLifetime == $maxLifetime) {
            return;
        }
        $this->maxLifetime = $maxLifetime;
        $this->createPool();
    }

    /**
     * @param float $waitTimeout
     */
    public function setPoolWaitTimeout(float $waitTimeout)
    {
        if ($this->waitTimeout == $waitTimeout) {
            return;
        }
        $this->waitTimeout = $waitTimeout;
        $this->createPool();
    }

    /**
     * @return array
     */
    public function poolStats(): array
    {
        if (!$this->pool) {
            return [];
        }
        return $this->pool->stats();
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Borrow connection
     * @return Connection
     * @throws WaitTimeoutException
     */
    protected function borrow(): Connection
    {
        if ($this->pool) {
            $driver = $this->pool->borrow();
            $conn = new Connection($driver, $this->logger);
        } else {
            $conn = new Connection($this->driver, $this->logger);
        }
        return $conn;
    }

    /**
     * Call
     * @param $command
     * @param $arguments
     * @return mixed
     * @throws \RedisException
     */
    public function __call($command, $arguments)
    {
        return $this->borrow()->__call($command, $arguments);
    }

}
