<?php

namespace App\Models;

use Config\Database;
use React\EventLoop\LoopInterface;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

class DataModel
{
    private $db;

    public function __construct(LoopInterface $loop)
    {
        // Obtener la conexión a la base de datos
        $this->db = Database::getConnection($loop);
    }

    // Obtener todos los productos
    public function getAll()
    {
        $query = "SELECT * FROM productos";
        return $this->db->query($query);
    }

    // Crear un nuevo producto
    public function create($nombre, $descripcion, $precio, $stock, $categoria)
    {
        $query = 'INSERT INTO productos (nombre, descripcion, precio, stock, categoria) VALUES (?, ?, ?, ?, ?)';
        return $this->db->query($query, [$nombre, $descripcion, $precio, $stock, $categoria]);
    }

    // Actualizar un producto
    public function update($id, $nombre, $descripcion, $precio, $stock, $categoria)
    {
        $query = 'UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria = ? WHERE id = ?';
        return $this->db->query($query, [$nombre, $descripcion, $precio, $stock, $categoria, $id]);
    }

    // Eliminar un producto
    public function delete($id): PromiseInterface
    {
        $query = 'DELETE FROM productos WHERE id = ?';

        // Retornar una promesa que se resuelve cuando la consulta se haya completado
        return $this->db->query($query, [$id])->then(
            function ($result) {
                return $result;
            },
            function ($error) {
                throw new \Exception('Error al eliminar el producto: ' . $error->getMessage());
            }
        );
    }

    // Obtiene un producto por ID
    public function getById($id)
    {
        $query = "SELECT * FROM productos WHERE id = ?";
        return $this->db->query($query, [$id])->then(function (QueryResult $result) {
            if (!empty($result->resultRows)) {
                return $result->resultRows[0];
            }
            return null;
        });
    }
}
