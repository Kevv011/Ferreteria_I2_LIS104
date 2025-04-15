<?php

namespace App\Controllers;

use App\Models\DataModel;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class DataController
{
    private $dataModel;

    public function __construct($loop)
    {
        // Instancia del modelo con loop
        $this->dataModel = new DataModel($loop);
    }

    // Metodo para manejar la solicitud para obtener los productos
    public function show(ServerRequestInterface $request, $loop): PromiseInterface
    {
        $acceptHeader = $request->getHeaderLine('Accept');

        if (str_contains($acceptHeader, 'text/html')) {
            $html = file_get_contents(__DIR__ . '/../views/products.html');
            return \React\Promise\resolve(new Response(
                200,
                ['Content-Type' => 'text/html'],
                $html
            ));
        }

        return $this->dataModel->getAll()->then(
            function ($result) {
                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode($result)
                );
            },
            function (\Exception $e) {
                return new Response(500, [], 'Error en la consulta');
            }
        );
    }

    // Metodo para procesar el ver productos individuales
    public function viewProduct(ServerRequestInterface $request, $loop, $id)
    {
        //Error log para ver el ID de un producto en consola y saber que se pasa correctamente 
        error_log("ID recibido en viewProduct: " . var_export($id, true));

        return $this->dataModel->getById($id)->then(
            function ($product) {
                if ($product) {
                    $html = file_get_contents(__DIR__ . '/../views/verProduct.html');
                    $html = str_replace('{{nombre}}', $product['nombre'], $html);
                    $html = str_replace('{{descripcion}}', $product['descripcion'], $html);
                    $html = str_replace('{{precio}}', $product['precio'], $html);
                    $html = str_replace('{{stock}}', $product['stock'], $html);
                    $html = str_replace('{{categoria}}', $product['categoria'], $html);
                    return new Response(200, ['Content-Type' => 'text/html'], $html);
                } else {
                    return new Response(404, [], 'Producto no encontrado');
                }
            },
            function (\Exception $e) {
                error_log($e->getMessage());
                error_log($e->getTraceAsString());
                return new Response(500, [], 'Error al obtener el producto');
            }
        );
    }

    // Metodo para mostrar el Form de creacion de productos
    public function createForm(ServerRequestInterface $request)
    {
        $html = file_get_contents(__DIR__ . '/../views/addProducts.html');
        return new Response(200, ['Content-Type' => 'text/html'], $html);
    }

    // Metodo para rear un nuevo producto
    public function create(ServerRequestInterface $request): PromiseInterface
    {
        $data = $request->getParsedBody(); // Obtener los datos del formulario

        // Validación de los campos
        if (empty($data['nombre']) || empty($data['descripcion']) || empty($data['precio']) || empty($data['stock']) || empty($data['categoria'])) {
            return \React\Promise\resolve(new Response(400, [], 'Faltan datos'));
        }

        // Insertar el producto en la base de datos
        return $this->dataModel->create(
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['stock'],
            $data['categoria']
        )->then(
            function () {
                // Redireccion a la vista de productos después de agregar el producto
                return new Response(
                    303,
                    ['Location' => '/productos'],
                    'Producto creado con éxito'
                );
            },
            function () {
                return new Response(500, [], 'Error al crear el producto');
            }
        );
    }

    // Metodo para actualizar un producto
    public function update(ServerRequestInterface $request): PromiseInterface
    {
        $path = $request->getUri()->getPath();
        $productId = basename($path);

        $data = json_decode((string) $request->getBody(), true);

        if (empty($data['nombre']) || empty($data['descripcion']) || empty($data['precio']) || empty($data['stock']) || empty($data['categoria'])) {
            return \React\Promise\resolve(new Response(400, [], 'Faltan datos'));
        }

        return $this->dataModel->update(
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
    public function deleteProduct(ServerRequestInterface $request): PromiseInterface
    {
        // Obtener el ID del producto desde la URL
        $productId = basename($request->getUri()->getPath());

        // Verificar si el producto existe
        return $this->dataModel->getById($productId)->then(
            function ($product) use ($productId) {

                if ($product) {
                    return $this->dataModel->delete($productId)->then(
                        function () {
                            return new Response(200, [], 'Producto eliminado');
                        },
                        function () {
                            return new Response(500, [], 'Error al eliminar el producto');
                        }
                    );
                } else {
                    return new Response(404, [], 'Producto no encontrado');
                }
            },
            function () {
                return new Response(500, [], 'Error al verificar la existencia del producto');
            }
        );
    }
}
