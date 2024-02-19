<?php

namespace Haoa\JsonRpc\Client\Pool;

use Mix\ObjectPool\DialerInterface;
use Haoa\JsonRpc\Client\Driver;

/**
 * Class Dialer
 */
class Dialer implements DialerInterface
{

    /**
     * 主机
     * @var string
     */
    public $host = '';

    /**
     * 端口
     * @var int
     */
    public $port = 0;

    public $socketType;

    /**
     * 超时
     * @var float
     */
    public $timeout = 5.0;

    /**
     * Dialer constructor.
     */
    public function __construct(string $host, int $port, $socketType = SWOOLE_SOCK_TCP, float $timeout = 5.0)
    {
        $this->host = $host;
        $this->port = $port;
        $this->socketType = $socketType;
        $this->timeout = $timeout;
    }

    /**
     * @return Driver
     */
    public function dial(): object
    {
        return new Driver(
            $this->host,
            $this->port,
            $this->socketType,
            $this->timeout,
        );
    }

}
