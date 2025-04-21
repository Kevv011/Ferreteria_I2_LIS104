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
    $method = $request->getMethod();
    $path = $request->getUri()->getPath();

    // === SERVIR ARCHIVOS ESTÁTICOS ===
    $publicPath = __DIR__ . '/public'; 
    $filePath = realpath($publicPath . $path);

    // Evita acceder fuera del directorio público
    if ($filePath && strpos($filePath, realpath($publicPath)) === 0 && is_file($filePath)) {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];

        $mime = $mimeTypes[$extension] ?? 'application/octet-stream';
        $contents = file_get_contents($filePath);

        return new Response(200, ['Content-Type' => $mime], $contents);
    }

    // === RUTEO NORMAL ===
    if (isset($routes[$method])) {
        foreach ($routes[$method] as $route => [$controllerClass, $action]) {
            $pattern = preg_replace('#\{[^}]+\}#', '([^/]+)', $route);
            if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
                array_shift($matches);
                $controller = new $controllerClass($loop);
                return $controller->$action($request, $loop, ...$matches);
            }
        }
    }

    return new Response(404, [], 'Ruta no encontrada');
});

// Iniciar servidor en el puerto 8080
$socket = new SocketServer('127.0.0.1:8080', [], $loop);
$server->listen($socket);

echo "Servidor corriendo en http://127.0.0.1:8080\n";

// Iniciar el Event Loop
$loop->run();
