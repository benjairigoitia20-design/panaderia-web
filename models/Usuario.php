<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function obtenerTodos($activos = true) {
        $sql = "SELECT u.*, r.nombre as rol_nombre 
                FROM usuarios u 
                LEFT JOIN roles r ON r.id = u.rol_id";
        if ($activos) $sql .= " WHERE u.activo = 1";
        $sql .= " ORDER BY u.nombre, u.apellido";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT u.*, r.nombre as rol_nombre FROM usuarios u LEFT JOIN roles r ON r.id = u.rol_id WHERE u.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorEmail($email) {
        $stmt = $this->pdo->prepare("SELECT u.*, r.nombre as rol_nombre FROM usuarios u LEFT JOIN roles r ON r.id = u.rol_id WHERE u.email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerRoles() {
        $stmt = $this->pdo->query("SELECT * FROM roles ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarUltimoAcceso($id) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function crear($datos) {
        if (empty($datos['password'])) return false;
        $sql = "INSERT INTO usuarios (nombre, apellido, email, telefono, password, rol_id, activo, foto, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['apellido'] ?? null,
            $datos['email'],
            $datos['telefono'] ?? null,
            password_hash($datos['password'], PASSWORD_DEFAULT),
            $datos['rol_id'],
            $datos['activo'] ?? 1,
            $datos['foto'] ?? null
        ]);
    }

    public function actualizar($id, $datos) {
        $campos = ['nombre = ?','apellido = ?','email = ?','telefono = ?','rol_id = ?','activo = ?'];
        $params = [$datos['nombre'], $datos['apellido'] ?? null, $datos['email'], $datos['telefono'] ?? null, $datos['rol_id'], $datos['activo'] ?? 1];
        if (!empty($datos['password'])) {
            $campos[] = 'password = ?';
            $params[] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }
        if (!empty($datos['foto'])) {
            $campos[] = 'foto = ?';
            $params[] = $datos['foto'];
        }
        $params[] = $id;
        $sql = "UPDATE usuarios SET " . implode(', ', $campos) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeEmail($email, $excluirId = null) {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
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