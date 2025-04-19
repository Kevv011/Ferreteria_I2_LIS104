<?php

use App\Controllers\HomeController;
use App\Controllers\DataController;

return [
    'GET' => [
        '/' => [HomeController::class, 'index'],
        '/productos' => [DataController::class, 'show'],
        '/productos/createForm' => [DataController::class, 'createForm'],
        '/productos/{id}' => [DataController::class, 'viewProduct'],
    ],
    'POST' => [
        '/productos/create' => [DataController::class, 'create'],
        '/productos/update' => [DataController::class, 'update'],
    ],
    'DELETE' => [
        '/productos/{id}/delete' => [DataController::class, 'delete'],
    ],
];
