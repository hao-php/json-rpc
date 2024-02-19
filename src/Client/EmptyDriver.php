<?php

namespace Haoa\JsonRpc\Client;

/**
 * Class EmptyDriver
 * @package Haoa\JsonRpc\Client
 */
class EmptyDriver
{

    protected $errorMessage = 'The connection has been returned to the pool, the current operation cannot be performed';

    public function __construct()
    {
    }

    public function instance(): \Redis
    {
        throw new \RuntimeException($this->errorMessage);
    }

    public function connect()
    {
        throw new \RuntimeException($this->errorMessage);
    }

    public function close()
    {
        throw new \RuntimeException($this->errorMessage);
    }

}
