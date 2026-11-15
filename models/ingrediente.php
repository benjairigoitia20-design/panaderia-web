<?php
require_once __DIR__ . '/../config/database.php';

class Ingrediente {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function obtenerTodos($activos = true) {
        $sql = "SELECT i.*, u.nombre as unidad_nombre, u.abreviatura as unidad_abreviatura 
                FROM ingredientes i 
                LEFT JOIN unidades_medida u ON i.unidad_medida_id = u.id";
        if ($activos) {
            $sql .= " WHERE i.estado = 1";
        }
        $sql .= " ORDER BY i.nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT i.*, u.nombre as unidad_nombre, u.abreviatura as unidad_abreviatura 
                FROM ingredientes i 
                LEFT JOIN unidades_medida u ON i.unidad_medida_id = u.id 
                WHERE i.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerConStockBajo() {
        $sql = "SELECT * FROM ingredientes WHERE stock_actual <= stock_minimo AND estado = 1 ORDER BY nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO ingredientes (nombre, codigo, categoria, unidad_medida_id, stock_actual, stock_minimo, costo_unitario, proveedor_principal, fecha_vencimiento, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['codigo'] ?? null,
            $datos['categoria'] ?? null,
            $datos['unidad_medida_id'],
            $datos['stock_actual'] ?? 0,
            $datos['stock_minimo'] ?? 0,
            $datos['costo_unitario'] ?? 0,
            $datos['proveedor_principal'] ?? null,
            $datos['fecha_vencimiento'] ?? null,
            $datos['estado'] ?? 1
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE ingredientes SET 
                nombre = ?, codigo = ?, categoria = ?, unidad_medida_id = ?, 
                stock_actual = ?, stock_minimo = ?, costo_unitario = ?, 
                proveedor_principal = ?, fecha_vencimiento = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['codigo'] ?? null,
            $datos['categoria'] ?? null,
            $datos['unidad_medida_id'],
            $datos['stock_actual'] ?? 0,
            $datos['stock_minimo'] ?? 0,
            $datos['costo_unitario'] ?? 0,
            $datos['proveedor_principal'] ?? null,
            $datos['fecha_vencimiento'] ?? null,
            $datos['estado'] ?? 1,
            $id
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("UPDATE ingredientes SET estado = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeNombre($nombre, $excluirId = null) {
        $sql = "SELECT id FROM ingredientes WHERE nombre = ? AND estado = 1";
        $params = [$nombre];
        if ($excluirId) {
            $sql .= " AND id != ?";
            $params[] = $excluirId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ? true : false;
    }

    public function actualizarStock($id, $nuevoStock) {
        $stmt = $this->pdo->prepare("UPDATE ingredientes SET stock_actual = ? WHERE id = ?");
        return $stmt->execute([$nuevoStock, $id]);
    }
}
?>