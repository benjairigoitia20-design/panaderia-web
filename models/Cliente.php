<?php
require_once __DIR__ . '/../config/database.php';

class Cliente {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function obtenerTodos($activos = true) {
        $sql = "SELECT * FROM clientes";
        if ($activos) {
            $sql .= " WHERE estado = 1";
        }
        $sql .= " ORDER BY nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscar($termino) {
        $sql = "SELECT * FROM clientes 
                WHERE (nombre LIKE ? OR apellido LIKE ? OR telefono LIKE ? OR email LIKE ?) 
                AND estado = 1 
                ORDER BY nombre 
                LIMIT 20";
        $termino = "%$termino%";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$termino, $termino, $termino, $termino]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO clientes (nombre, apellido, telefono, email, direccion, fecha_nacimiento, observaciones, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['apellido'] ?? null,
            $datos['telefono'] ?? null,
            $datos['email'] ?? null,
            $datos['direccion'] ?? null,
            $datos['fecha_nacimiento'] ?? null,
            $datos['observaciones'] ?? null,
            $datos['estado'] ?? 1
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE clientes SET 
                nombre = ?, apellido = ?, telefono = ?, email = ?, 
                direccion = ?, fecha_nacimiento = ?, observaciones = ?, estado = ? 
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['apellido'] ?? null,
            $datos['telefono'] ?? null,
            $datos['email'] ?? null,
            $datos['direccion'] ?? null,
            $datos['fecha_nacimiento'] ?? null,
            $datos['observaciones'] ?? null,
            $datos['estado'] ?? 1,
            $id
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("UPDATE clientes SET estado = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeEmail($email, $excluirId = null) {
        $sql = "SELECT id FROM clientes WHERE email = ? AND estado = 1";
        $params = [$email];
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