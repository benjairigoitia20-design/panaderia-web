<?php
require_once __DIR__ . '/../config/database.php';

class Proveedor {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function obtenerTodos($activos = true) {
        $sql = "SELECT * FROM proveedores";
        if ($activos) {
            $sql .= " WHERE estado = 1";
        }
        $sql .= " ORDER BY razon_social";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM proveedores WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscar($termino) {
        $sql = "SELECT * FROM proveedores 
                WHERE (razon_social LIKE ? OR cuit LIKE ? OR telefono LIKE ? OR email LIKE ?) 
                AND estado = 1 
                ORDER BY razon_social 
                LIMIT 20";
        $termino = "%$termino%";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$termino, $termino, $termino, $termino]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO proveedores (razon_social, cuit, telefono, email, direccion, contacto_nombre, contacto_telefono, observaciones, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['razon_social'],
            $datos['cuit'] ?? null,
            $datos['telefono'] ?? null,
            $datos['email'] ?? null,
            $datos['direccion'] ?? null,
            $datos['contacto_nombre'] ?? null,
            $datos['contacto_telefono'] ?? null,
            $datos['observaciones'] ?? null,
            $datos['estado'] ?? 1
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE proveedores SET 
                razon_social = ?, cuit = ?, telefono = ?, email = ?, 
                direccion = ?, contacto_nombre = ?, contacto_telefono = ?, 
                observaciones = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['razon_social'],
            $datos['cuit'] ?? null,
            $datos['telefono'] ?? null,
            $datos['email'] ?? null,
            $datos['direccion'] ?? null,
            $datos['contacto_nombre'] ?? null,
            $datos['contacto_telefono'] ?? null,
            $datos['observaciones'] ?? null,
            $datos['estado'] ?? 1,
            $id
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("UPDATE proveedores SET estado = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeCuit($cuit, $excluirId = null) {
        $sql = "SELECT id FROM proveedores WHERE cuit = ? AND estado = 1";
        $params = [$cuit];
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