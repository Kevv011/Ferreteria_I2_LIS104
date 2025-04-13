<?php

use App\Controllers\DataController;
use App\Controllers\HomeController;

// Definir las rutas
return [
    '/' => [HomeController::class, 'index'],                           // Ruta principal
    '/productos' => [DataController::class, 'show'],                   // Leer todos los productos
    '/productos/{id}' => [DataController::class, 'viewProduct'],       // Leer productos individuales
    '/productos/createForm' => [DataController::class, 'createForm'],  // Muestra el form para crear un nuevo producto
    '/productos/create' => [DataController::class, 'create'],          // Crear un nuevo producto
    '/productos/{id}/update' => [DataController::class, 'update'],     // Actualizar un producto por ID
    '/productos/{id}/delete' => [DataController::class, 'delete'],     // Eliminar un producto por ID
];
