<?php
require_once __DIR__ . '/../config/database.php';

class MovimientoInventario {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function registrar($datos) {
        $sql = "INSERT INTO movimientos_inventario (
                    tipo_movimiento, producto_id, ingrediente_id, cantidad, 
                    stock_anterior, stock_nuevo, motivo, usuario_id, referencia_id, observacion
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['tipo_movimiento'],
            $datos['producto_id'] ?? null,
            $datos['ingrediente_id'] ?? null,
            $datos['cantidad'],
            $datos['stock_anterior'],
            $datos['stock_nuevo'],
            $datos['motivo'] ?? null,
            $datos['usuario_id'],
            $datos['referencia_id'] ?? null,
            $datos['observacion'] ?? null
        ]);
    }

    public function obtenerPorProducto($producto_id, $limite = 100) {
        $sql = "SELECT * FROM movimientos_inventario 
                WHERE producto_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$producto_id, $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorIngrediente($ingrediente_id, $limite = 100) {
        $sql = "SELECT * FROM movimientos_inventario 
                WHERE ingrediente_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ingrediente_id, $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUltimos($limite = 50) {
        $sql = "SELECT m.*, 
                u.nombre as usuario_nombre,
                p.nombre as producto_nombre,
                i.nombre as ingrediente_nombre
                FROM movimientos_inventario m
                LEFT JOIN usuarios u ON m.usuario_id = u.id
                LEFT JOIN productos p ON m.producto_id = p.id
                LEFT JOIN ingredientes i ON m.ingrediente_id = i.id
                ORDER BY m.created_at DESC 
                LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>