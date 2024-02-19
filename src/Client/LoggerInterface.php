<?php

namespace Haoa\JsonRpc\Client;

/**
 * Interface LoggerInterface
 */
interface LoggerInterface
{

    public function trace(float $time, string $cmd, array $args, ?\Throwable $exception): void;

}