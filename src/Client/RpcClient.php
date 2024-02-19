<?php

namespace Haoa\JsonRpc\Client;

class RpcClient
{

    /**
     * @var \Swoole\Coroutine\Client
     */
    protected $client;

    public function __construct($clint)
    {
        $this->client = $clint;
    }

    public function  getErrMsg()
    {
        return $this->client->errMsg;
    }

    public function send($data, $timeout = 5)
    {
        $len  = strlen($data);
        $size = $this->client->send($data, $timeout);
        if ($size === false) {
            throw new \Swoole\Exception($this->client->errMsg, $this->client->errCode);
        }
        if ($len !== $size) {
            throw new \Swoole\Exception('The sending data is incomplete, it may be that the socket has been closed by the peer.');
        }
    }

    public function recv(float $timeout = 5)
    {
        $data = $this->client->recv($timeout);
        if ($data === false) { // 接收失败
            throw new \Swoole\Exception($this->client->errMsg, $this->client->errCode);
        }
        if ($data === "") { // 连接关闭
            $errCode = stripos(PHP_OS, 'Darwin') !== false ? 54 : 104; // mac=54, linux=104
            $errMsg  = swoole_strerror($errCode, 9);
            throw new \Swoole\Exception($errMsg, $errCode);
        }
        return $data;
    }

    public function close()
    {
        return $this->client->close();
    }

    public function call($id, $method, $params = "", $timeout = 5)
    {
        $content = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => $id,
        ];
        $content = json_encode($content);
        $len = pack("N", strlen($content));
        $this->send($len . $content, $timeout);
        $data = $this->recv($timeout);
        $len = unpack("N", substr($data, 0, 4))[1];
        $data = substr($data, 4, $len);
        return json_decode($data, true);
    }

}