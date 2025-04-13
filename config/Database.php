<?php

namespace Config;

use React\EventLoop\LoopInterface;
use React\MySQL\Factory;
use React\MySQL\ConnectionInterface;

class Database
{
    private static ?ConnectionInterface $connection = null;

    // Conexion que usa el loop y es pasado como parametro
    public static function getConnection(LoopInterface $loop): ConnectionInterface
    {
        // Se la conexión si aún no ha sido creada
        if (self::$connection === null) {
            $factory = new Factory($loop);

            // Aquí se pasa el loop correctamente
            self::$connection = $factory->createLazyConnection(
                'root@localhost:3306/ferreteria'
            );
        }

        // Devolvemos la conexión
        return self::$connection;
    }
}
