<?php

use Haoa\JsonRpc\Client\Client as jsonRpcClient;
use function Swoole\Coroutine\run;

require 'autoload.php';

run(function () {
    //$client = new jsonRpcClient('127.0.0.1', 8082);
    $client = new jsonRpcClient('/tmp/rpc.socket', 0, SWOOLE_SOCK_UNIX_STREAM);
    $maxOpen = 100;        // 最大开启连接数
    $maxIdle = 20;        // 最大闲置连接数
    $maxLifetime = 3600;  // 连接的最长生命周期
    $waitTimeout = 5;   // 从池获取连接等待的时间, 0为一直等待
    $client->startPool($maxOpen, $maxIdle, $maxLifetime, $waitTimeout);

    $ret = $client->call(1, 'Hello.Index');
    var_dump($ret);
    $client->close();
    $ret = $client->call(1, 'Hello.Index');
    var_dump($ret);
});
