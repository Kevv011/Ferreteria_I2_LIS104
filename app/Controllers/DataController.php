<?php

namespace App\Controllers;

use App\Models\DataModel;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class DataController
{
    private $productModel;

    public function __construct($loop)
    {
        // Instanciamos el modelo y le pasamos el loop
        $this->productModel = new DataModel($loop);
    }

    // Manejar la solicitud para obtener los productos
    public function handle(ServerRequestInterface $request, $loop): PromiseInterface
    {
        return $this->productModel->getAll()->then(
            function ($result) {
                // Devolver los resultados como JSON
                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode($result)
                );
            },
            function (\Exception $e) {
                // Manejo de errores
                return new Response(500, [], 'Error en la consulta');
            }
        );
    }

    // Crear un nuevo producto
    public function create(ServerRequestInterface $request): PromiseInterface
    {
        $data = json_decode((string) $request->getBody(), true);

        if (empty($data['nombre']) || empty($data['descripcion']) || empty($data['precio']) || empty($data['stock']) || empty($data['categoria'])) {
            // Se devuelve una promesa que resuelve un Response con error
            return \React\Promise\resolve(new Response(400, [], 'Faltan datos'));
        }

        return $this->productModel->create(
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['stock'],
            $data['categoria']
        )->then(
            function () {
                return new Response(201, [], 'Producto creado');
            },
            function () {
                return new Response(500, [], 'Error al crear el producto');
            }
        );
    }

    // Actualizar un producto
    public function update(ServerRequestInterface $request): PromiseInterface
    {
        $path = $request->getUri()->getPath();
        $productId = basename($path);

        $data = json_decode((string) $request->getBody(), true);

        if (empty($data['nombre']) || empty($data['descripcion']) || empty($data['precio']) || empty($data['stock']) || empty($data['categoria'])) {
            return \React\Promise\resolve(new Response(400, [], 'Faltan datos'));
        }

        return $this->productModel->update(
            $productId,
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['stock'],
            $data['categoria']
        )->then(
            function () {
                return new Response(200, [], 'Producto actualizado');
            },
            function () {
                return new Response(500, [], 'Error al actualizar el producto');
            }
        );
    }

    // Eliminar un producto
    public function delete(ServerRequestInterface $request): PromiseInterface
    {
        $path = $request->getUri()->getPath();
        $productId = basename($path);

        return $this->productModel->delete($productId)->then(
            function () {
                return new Response(200, [], 'Producto eliminado');
            },
            function () {
                return new Response(500, [], 'Error al eliminar el producto');
            }
        );
    }
}
