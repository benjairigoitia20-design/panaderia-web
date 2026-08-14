<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Ingrediente.php';

class Receta {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function obtenerTodos($activos = true) {
        $sql = "SELECT r.*, p.nombre as producto_nombre, p.precio as producto_precio 
                FROM recetas r 
                LEFT JOIN productos p ON r.producto_id = p.id";
        if ($activos) {
            $sql .= " WHERE r.estado = 1";
        }
        $sql .= " ORDER BY r.nombre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT r.*, p.nombre as producto_nombre, p.precio as producto_precio 
                FROM recetas r 
                LEFT JOIN productos p ON r.producto_id = p.id 
                WHERE r.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorProducto($producto_id) {
        $sql = "SELECT * FROM recetas WHERE producto_id = ? AND estado = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$producto_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerIngredientes($receta_id) {
        $sql = "SELECT ri.*, i.nombre as ingrediente_nombre, i.costo_unitario, 
                u.nombre as unidad_nombre, u.abreviatura as unidad_abreviatura
                FROM receta_ingredientes ri
                LEFT JOIN ingredientes i ON ri.ingrediente_id = i.id
                LEFT JOIN unidades_medida u ON ri.unidad_medida_id = u.id
                WHERE ri.receta_id = ?
                ORDER BY i.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$receta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calcularCostos($receta_id) {
        $ingredientes = $this->obtenerIngredientes($receta_id);
        $costo_total = 0;
        
        foreach ($ingredientes as $ing) {
            $costo_parcial = $ing['cantidad'] * $ing['costo_unitario'];
            $costo_total += $costo_parcial;
        }

        return $costo_total;
    }

    public function crear($datos) {
        // Iniciar transacción
        $this->pdo->beginTransaction();
        
        try {
            $sql = "INSERT INTO recetas (producto_id, nombre, rendimiento, unidad_rendimiento, 
                    tiempo_preparacion, tiempo_coccion, instrucciones, costo_total, costo_por_unidad, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $datos['producto_id'],
                $datos['nombre'],
                $datos['rendimiento'],
                $datos['unidad_rendimiento'],
                $datos['tiempo_preparacion'] ?? 0,
                $datos['tiempo_coccion'] ?? 0,
                $datos['instrucciones'] ?? null,
                0, // costo_total temporal
                0, // costo_por_unidad temporal
                $datos['estado'] ?? 1
            ]);

            if (!$result) {
                throw new Exception("Error al crear receta");
            }

            $receta_id = $this->pdo->lastInsertId();

            // Guardar ingredientes
            if (isset($datos['ingredientes']) && is_array($datos['ingredientes'])) {
                foreach ($datos['ingredientes'] as $ing) {
                    $sql_ing = "INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad_medida_id, costo_parcial) 
                                VALUES (?, ?, ?, ?, ?)";
                    $stmt_ing = $this->pdo->prepare($sql_ing);
                    
                    // Obtener costo del ingrediente
                    $ingredienteModel = new Ingrediente();
                    $ingrediente = $ingredienteModel->obtenerPorId($ing['ingrediente_id']);
                    $costo_parcial = $ing['cantidad'] * ($ingrediente['costo_unitario'] ?? 0);
                    
                    $stmt_ing->execute([
                        $receta_id,
                        $ing['ingrediente_id'],
                        $ing['cantidad'],
                        $ing['unidad_medida_id'],
                        $costo_parcial
                    ]);
                }
            }

            // Actualizar costos
            $costo_total = $this->calcularCostos($receta_id);
            $receta = $this->obtenerPorId($receta_id);
            $costo_por_unidad = $receta['rendimiento'] > 0 ? $costo_total / $receta['rendimiento'] : 0;

            $update = $this->pdo->prepare("UPDATE recetas SET costo_total = ?, costo_por_unidad = ? WHERE id = ?");
            $update->execute([$costo_total, $costo_por_unidad, $receta_id]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function actualizar($id, $datos) {
        $this->pdo->beginTransaction();
        
        try {
            $sql = "UPDATE recetas SET 
                    producto_id = ?, nombre = ?, rendimiento = ?, unidad_rendimiento = ?, 
                    tiempo_preparacion = ?, tiempo_coccion = ?, instrucciones = ?, estado = ? 
                    WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $datos['producto_id'],
                $datos['nombre'],
                $datos['rendimiento'],
                $datos['unidad_rendimiento'],
                $datos['tiempo_preparacion'] ?? 0,
                $datos['tiempo_coccion'] ?? 0,
                $datos['instrucciones'] ?? null,
                $datos['estado'] ?? 1,
                $id
            ]);

            if (!$result) {
                throw new Exception("Error al actualizar receta");
            }

            // Eliminar ingredientes existentes
            $delete = $this->pdo->prepare("DELETE FROM receta_ingredientes WHERE receta_id = ?");
            $delete->execute([$id]);

            // Guardar nuevos ingredientes
            if (isset($datos['ingredientes']) && is_array($datos['ingredientes'])) {
                foreach ($datos['ingredientes'] as $ing) {
                    $sql_ing = "INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, unidad_medida_id, costo_parcial) 
                                VALUES (?, ?, ?, ?, ?)";
                    $stmt_ing = $this->pdo->prepare($sql_ing);
                    
                    $ingredienteModel = new Ingrediente();
                    $ingrediente = $ingredienteModel->obtenerPorId($ing['ingrediente_id']);
                    $costo_parcial = $ing['cantidad'] * ($ingrediente['costo_unitario'] ?? 0);
                    
                    $stmt_ing->execute([
                        $id,
                        $ing['ingrediente_id'],
                        $ing['cantidad'],
                        $ing['unidad_medida_id'],
                        $costo_parcial
                    ]);
                }
            }

            // Actualizar costos
            $costo_total = $this->calcularCostos($id);
            $receta = $this->obtenerPorId($id);
            $costo_por_unidad = $receta['rendimiento'] > 0 ? $costo_total / $receta['rendimiento'] : 0;

            $update = $this->pdo->prepare("UPDATE recetas SET costo_total = ?, costo_por_unidad = ? WHERE id = ?");
            $update->execute([$costo_total, $costo_por_unidad, $id]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("UPDATE recetas SET estado = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function existeNombre($nombre, $excluirId = null) {
        $sql = "SELECT id FROM recetas WHERE nombre = ? AND estado = 1";
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