<?php

namespace Haoa\JsonRpc\Client;

use Mix\ObjectPool\ObjectTrait;

/**
 * Class Driver
 * @package Haoa\JsonRpc\Client
 */
class Driver
{

    use ObjectTrait;

    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var int
     */
    protected $port = 0;

    /**
     *
     */
    protected $socketType;

    /**
     * @var float
     */
    protected $timeout = 5.0;

    /**
     * @var RpcClient
     */
    protected $client;

    /**
     * Driver constructor.
     */
    public function __construct(string $host, int $port, $socketType = SWOOLE_SOCK_TCP, float $timeout = 5.0)
    {
        $this->host = $host;
        $this->port = $port;
        $this->socketType = $socketType;
        $this->timeout = $timeout;
        $this->connect();
    }

    /**
     * Get instance
     */
    public function instance(): RpcClient
    {
        return $this->client;
    }

    /**
     * Connect
     */
    public function connect()
    {
        $client = new \Swoole\Coroutine\Client($this->socketType);
        if (!$client->connect($this->host, $this->port, $this->timeout)) {
            throw new \Swoole\Exception(sprintf("JSON-RPC: %s (host:%s, port: %s)", $client->errMsg, $this->host, $this->port), $client->errCode);
        }
        $client->set([
            'open_length_check' => true,
            'package_max_length' => 81920,
            'package_length_type' => 'N', //see php pack()
            'package_length_offset' => 0,
            'package_body_offset' => 4,
        ]);
        $this->client = new RpcClient($client);
    }

    /**
     * Close
     */
    public function close()
    {
        $this->client->close();
        $this->client = null;
    }

}
