<?php
//Permite visualizar errores que pueda generar el server
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use React\Http\HttpServer;
use React\EventLoop\Factory;
use React\Socket\SocketServer;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Promise;

// Crear Event Loop
$loop = Factory::create();

// Cargar rutas
$routes = require __DIR__ . '/routes/web.php';

// Función para simular lectura no bloqueante
function asyncReadFile($filePath)
{
    return new Promise(function ($resolve, $reject) use ($filePath) {
        if (!file_exists($filePath)) {
            $reject('Archivo no encontrado');
            return;
        }

        $contents = @file_get_contents($filePath);
        if ($contents === false) {
            $reject('Error al leer el archivo');
            return;
        }

        $resolve($contents);
    });
}

// Crear servidor HTTP
$server = new HttpServer(function (ServerRequestInterface $request) use ($routes, $loop) {
    $path = $request->getUri()->getPath();

    // Rutas para archivos estáticos como CSS o imágenes
    if (preg_match('#^/(css|img)/(.+)$#', $path, $matches)) {
        $file = __DIR__ . '/public/' . $matches[1] . '/' . $matches[2];

        return asyncReadFile($file)->then(
            function ($contents) use ($file) {
                $mime = mime_content_type($file);
                return new Response(200, ['Content-Type' => $mime], $contents);
            },
            function ($error) {
                return new Response(404, [], $error);
            }
        );
    }

    // Rutas definidas en el archivo de rutas
    var_dump("Ruta solicitada: " . $path);
    if (isset($routes[$path])) {
        try {
            [$controllerClass, $method] = $routes[$path];
            $controller = new $controllerClass();
            return $controller->$method($request, $loop);
        } catch (\Throwable $e) {
            // Mostrar error como respuesta HTTP
            return new Response(500, ['Content-Type' => 'text/plain'], "Error en controlador: " . $e->getMessage());
        }
    }

    return new Response(404, [], 'Página no encontrada');
});

// Iniciar servidor en el puerto 8080
$socket = new SocketServer('127.0.0.1:8080', [], $loop);
$server->listen($socket);

echo "Servidor corriendo en http://127.0.0.1:8080\n";

// Iniciar el Event Loop
$loop->run();
