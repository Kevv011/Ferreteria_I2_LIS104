<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

use React\Http\HttpServer;
use React\EventLoop\Factory;
use React\Socket\SocketServer;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\Promise;
use Psr\Http\Message\ResponseInterface;

// Crear Event Loop
$loop = Factory::create();

// Cargar rutas
$routes = require __DIR__ . '/routes/web.php';

// Crear servidor HTTP
$server = new HttpServer(function (ServerRequestInterface $request) use ($routes, $loop) {
    $method = $request->getMethod();        // Obtener el método de la solicitud
    $path = $request->getUri()->getPath();  // Obtener la ruta solicitada

    // Verificar si la ruta existe para el método HTTP
    if (isset($routes[$method])) {

        // Foreach para recorrer las rutas definidas para el método correspondiente
        foreach ($routes[$method] as $route => [$controllerClass, $action]) {
            $pattern = preg_replace('#\{[^}]+\}#', '([^/]+)', $route); // Expresiones regulares para rutas dinamicas como /productos/{id}
            if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
                error_log("Ruta detectada: $route");                                // error_log para ver en consola la ruta accedida
                error_log("Coincidencias encontradas: " . print_r($matches, true)); // error_log para ver si se encontro una ruta conincidente

                // Se llama al controlador correspondiente y pasar el request
                array_shift($matches); 
                $controller = new $controllerClass($loop);
                return $controller->$action($request, $loop, ...$matches); 
            }
        }
    }

    // Si no se encontró una coincidencia, devolver status 404
    return new Response(404, [], 'Ruta no encontrada');
});

// Iniciar servidor en el puerto 8080
$socket = new SocketServer('127.0.0.1:8080', [], $loop);
$server->listen($socket);

echo "Servidor corriendo en http://127.0.0.1:8080\n";

// Iniciar el Event Loop
$loop->run();
