<?php

namespace App\Controllers;

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

class HomeController
{
    public function index(ServerRequestInterface $request)
    {
        $filePath = __DIR__ . '/../Views/home.html';

        // Verificar si el archivo existe antes de intentar cargarlo
        if (!file_exists($filePath)) {
            return new Response(404, [], 'Página no encontrada');
        }

        // Leer el archivo HTML
        $html = file_get_contents($filePath);

        // Retornar la respuesta con el contenido HTML
        return new Response(200, ['Content-Type' => 'text/html'], $html);
    }
}

