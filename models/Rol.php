<?php
require_once __DIR__ . '/../config/database.php';

class Rol {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function obtenerTodos() {
        $sql = "SELECT r.*, (SELECT COUNT(*) FROM usuarios u WHERE u.rol_id = r.id AND u.activo = 1) as usuarios_count
                FROM roles r
                ORDER BY r.nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPermisos() {
        $stmt = $this->pdo->query("SELECT * FROM permisos ORDER BY modulo, nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPermisosPorRol($rolId) {
        $stmt = $this->pdo->prepare("SELECT permiso_id FROM roles_permisos WHERE rol_id = ?");
        $stmt->execute([$rolId]);
        return array_map(function ($item) {
            return (int)$item['permiso_id'];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function crear($nombre) {
        $stmt = $this->pdo->prepare("INSERT INTO roles (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    public function actualizar($id, $nombre) {
        $stmt = $this->pdo->prepare("UPDATE roles SET nombre = ? WHERE id = ?");
        return $stmt->execute([$nombre, $id]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE rol_id = ? AND activo = 1");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return false;
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("DELETE FROM roles_permisos WHERE rol_id = ?");
            $stmt->execute([$id]);

            $stmt = $this->pdo->prepare("DELETE FROM roles WHERE id = ?");
            $stmt->execute([$id]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function actualizarPermisos($rolId, $permisos) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("DELETE FROM roles_permisos WHERE rol_id = ?");
            $stmt->execute([$rolId]);

            if (!empty($permisos)) {
                $sql = "INSERT INTO roles_permisos (rol_id, permiso_id) VALUES (?, ?)";
                $stmt = $this->pdo->prepare($sql);
                foreach ($permisos as $permisoId) {
                    $stmt->execute([$rolId, (int)$permisoId]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function existeNombre($nombre, $excluirId = null) {
        $sql = "SELECT id FROM roles WHERE nombre = ?";
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
