<?php
require_once __DIR__ . '/../config/database.php';

class UnidadMedida {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function obtenerTodos($activos = true) {
        $sql = "SELECT * FROM unidades_medida";
        if ($activos) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM unidades_medida WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO unidades_medida (nombre, abreviatura, activo) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['abreviatura'],
            $datos['activo'] ?? 1
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE unidades_medida SET nombre = ?, abreviatura = ?, activo = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['abreviatura'],
            $datos['activo'] ?? 1,
            $id
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("UPDATE unidades_medida SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeNombre($nombre, $excluirId = null) {
        $sql = "SELECT id FROM unidades_medida WHERE nombre = ? AND activo = 1";
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