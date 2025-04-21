<?php

namespace Config;

use React\EventLoop\LoopInterface;
use React\MySQL\Factory;
use React\MySQL\ConnectionInterface;

class Database
{
    private static ? ConnectionInterface $connection = null;

    public static function getConnection(LoopInterface $loop): ConnectionInterface
    {
        if (self::$connection === null) {
            $factory = new Factory($loop);

            // Credenciales de conexion
            self::$connection = $factory->createLazyConnection(
                'mysql://root@127.0.0.1:3306/ferreteria'
            );
        }

        return self::$connection;
    }
}
