<?php
require_once __DIR__ . '/../config/database.php';

class Producto {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function obtenerTodos($activos = true) {
        $sql = "SELECT * FROM productos";
        if ($activos) {
            $sql .= " WHERE estado = 1";
        }
        $sql .= " ORDER BY id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosConCategoria($activos = true) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id";
        if ($activos) {
            $sql .= " WHERE p.estado = 1";
        }
        $sql .= " ORDER BY p.id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, imagen, destacado, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['stock'],
            $datos['categoria_id'] ?? null,
            $datos['imagen'],
            $datos['destacado'] ?? 0,
            $datos['estado'] ?? 1
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE productos SET 
                nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria_id = ?, 
                imagen = ?, destacado = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'],
            $datos['precio'],
            $datos['stock'],
            $datos['categoria_id'] ?? null,
            $datos['imagen'],
            $datos['destacado'] ?? 0,
            $datos['estado'] ?? 1,
            $id
        ]);
    }

    public function eliminar($id) {
        // Soft delete: cambiamos estado a 0
        $stmt = $this->pdo->prepare("UPDATE productos SET estado = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeNombre($nombre, $excluirId = null) {
        $sql = "SELECT id FROM productos WHERE nombre = ? AND estado = 1";
        $params = [$nombre];
        if ($excluirId) {
            $sql .= " AND id != ?";
            $params[] = $excluirId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ? true : false;
    }
}
?>