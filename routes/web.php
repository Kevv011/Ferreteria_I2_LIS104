<?php

use App\Controllers\DataController;
use App\Controllers\HomeController;

// Definir las rutas
return [
    '/' => [HomeController::class, 'index'],                    // Ruta principal
    '/productos' => [DataController::class, 'handle'],          // Leer todos los productos
    '/productos/create' => [DataController::class, 'create'],   // Crear un nuevo producto
    '/productos/{id}' => [DataController::class, 'update'],     // Actualizar un producto por ID
    '/productos/{id}/delete' => [DataController::class, 'delete'], // Eliminar un producto por ID
];
