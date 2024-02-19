<?php

namespace Haoa\JsonRpc\Client\Pool;

use Haoa\JsonRpc\Client\Driver;
use Mix\ObjectPool\AbstractObjectPool;

/**
 * Class ConnectionPool
 */
class ConnectionPool extends AbstractObjectPool
{

    /**
     * 借用连接
     * @return Driver
     */
    public function borrow(): object
    {
        return parent::borrow();
    }

}
