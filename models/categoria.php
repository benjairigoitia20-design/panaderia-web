<?php
require_once __DIR__ . '/../config/database.php';

class Categoria {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function obtenerTodos($activos = true) {
        $sql = "SELECT * FROM categorias";
        if ($activos) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM categorias WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO categorias (nombre, descripcion, activo) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'],
            $datos['activo'] ?? 1
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE categorias SET nombre = ?, descripcion = ?, activo = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'],
            $datos['activo'] ?? 1,
            $id
        ]);
    }

    public function eliminar($id) {
        // Verificar si tiene productos asociados
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = ? AND estado = 1");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return false; // No se puede eliminar si tiene productos activos
        }
        // Soft delete
        $stmt = $this->pdo->prepare("UPDATE categorias SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeNombre($nombre, $excluirId = null) {
        $sql = "SELECT id FROM categorias WHERE nombre = ? AND activo = 1";
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