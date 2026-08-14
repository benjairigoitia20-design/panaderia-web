<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Receta.php';
require_once __DIR__ . '/Ingrediente.php';
require_once __DIR__ . '/MovimientoInventario.php';

class OrdenProduccion {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function obtenerTodos($activos = true) {
        $sql = "SELECT op.*, p.nombre as producto_nombre, r.nombre as receta_nombre,
                u.nombre as responsable_nombre
                FROM ordenes_produccion op
                LEFT JOIN productos p ON op.producto_id = p.id
                LEFT JOIN recetas r ON op.receta_id = r.id
                LEFT JOIN usuarios u ON op.responsable_id = u.id";
        if ($activos) {
            $sql .= " WHERE op.estado != 'cancelada'";
        }
        $sql .= " ORDER BY op.fecha_produccion DESC, op.id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT op.*, p.nombre as producto_nombre, r.nombre as receta_nombre,
                u.nombre as responsable_nombre
                FROM ordenes_produccion op
                LEFT JOIN productos p ON op.producto_id = p.id
                LEFT JOIN recetas r ON op.receta_id = r.id
                LEFT JOIN usuarios u ON op.responsable_id = u.id
                WHERE op.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerIngredientes($orden_id) {
        $sql = "SELECT pi.*, i.nombre as ingrediente_nombre, i.stock_actual,
                u.nombre as unidad_nombre, u.abreviatura as unidad_abreviatura
                FROM produccion_ingredientes pi
                LEFT JOIN ingredientes i ON pi.ingrediente_id = i.id
                LEFT JOIN unidades_medida u ON pi.unidad_medida_id = u.id
                WHERE pi.orden_produccion_id = ?
                ORDER BY i.nombre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orden_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generarNumero() {
        $year = date('Y');
        $month = date('m');
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM ordenes_produccion WHERE YEAR(created_at) = $year AND MONTH(created_at) = $month");
        $count = $stmt->fetchColumn() + 1;
        return 'OP-' . $year . $month . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function crear($datos) {
        // Iniciar transacción
        $this->pdo->beginTransaction();
        
        try {
            $numero = $this->generarNumero();
            
            $sql = "INSERT INTO ordenes_produccion (
                        numero, producto_id, receta_id, cantidad_planificada, 
                        fecha_produccion, responsable_id, estado, observaciones
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $numero,
                $datos['producto_id'],
                $datos['receta_id'],
                $datos['cantidad_planificada'],
                $datos['fecha_produccion'],
                $datos['responsable_id'],
                'planificada',
                $datos['observaciones'] ?? null
            ]);

            if (!$result) {
                throw new Exception("Error al crear orden de producción");
            }

            $orden_id = $this->pdo->lastInsertId();

            // Calcular ingredientes necesarios
            $recetaModel = new Receta();
            $ingredientes = $recetaModel->obtenerIngredientes($datos['receta_id']);
            
            foreach ($ingredientes as $ing) {
                $cantidad_necesaria = ($ing['cantidad'] / $recetaModel->obtenerPorId($datos['receta_id'])['rendimiento']) * $datos['cantidad_planificada'];
                
                $sql_ing = "INSERT INTO produccion_ingredientes (
                                orden_produccion_id, ingrediente_id, cantidad_teorica, cantidad_real, 
                                unidad_medida_id
                            ) VALUES (?, ?, ?, ?, ?)";
                $stmt_ing = $this->pdo->prepare($sql_ing);
                $stmt_ing->execute([
                    $orden_id,
                    $ing['ingrediente_id'],
                    $cantidad_necesaria,
                    0, // cantidad_real inicial en 0
                    $ing['unidad_medida_id']
                ]);
            }

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function iniciarProduccion($id) {
        $stmt = $this->pdo->prepare("UPDATE ordenes_produccion 
                                    SET estado = 'en_produccion', fecha_inicio = NOW() 
                                    WHERE id = ? AND estado = 'planificada'");
        return $stmt->execute([$id]);
    }

    public function finalizarProduccion($id, $cantidad_producida, $observaciones = null) {
        $this->pdo->beginTransaction();
        
        try {
            // Obtener orden
            $orden = $this->obtenerPorId($id);
            if (!$orden) {
                throw new Exception("Orden no encontrada");
            }

            // Actualizar orden
            $sql = "UPDATE ordenes_produccion 
                    SET estado = 'terminada', cantidad_producida = ?, 
                        fecha_fin = NOW(), observaciones = ? 
                    WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$cantidad_producida, $observaciones, $id]);

            // Obtener ingredientes de la orden
            $ingredientes = $this->obtenerIngredientes($id);
            
            $movimientoModel = new MovimientoInventario();
            
            foreach ($ingredientes as $ing) {
                $cantidad_real = ($ing['cantidad_teorica'] / $orden['cantidad_planificada']) * $cantidad_producida;
                $merma = $ing['cantidad_teorica'] - $cantidad_real;
                
                // Actualizar cantidad real
                $update = $this->pdo->prepare("UPDATE produccion_ingredientes 
                                              SET cantidad_real = ?, cantidad_merma = ? 
                                              WHERE id = ?");
                $update->execute([$cantidad_real, $merma, $ing['id']]);

                // Descontar ingredientes del stock
                $ingredienteModel = new Ingrediente();
                $ingrediente = $ingredienteModel->obtenerPorId($ing['ingrediente_id']);
                $stock_anterior = $ingrediente['stock_actual'];
                $stock_nuevo = $stock_anterior - $cantidad_real;

                // Actualizar stock
                $ingredienteModel->actualizarStock($ing['ingrediente_id'], $stock_nuevo);

                // Registrar movimiento
                $movimientoModel->registrar([
                    'tipo_movimiento' => 'salida_produccion',
                    'ingrediente_id' => $ing['ingrediente_id'],
                    'cantidad' => $cantidad_real,
                    'stock_anterior' => $stock_anterior,
                    'stock_nuevo' => $stock_nuevo,
                    'motivo' => 'Consumo en producción - Orden ' . $orden['numero'],
                    'usuario_id' => $_SESSION['usuario_id'],
                    'referencia_id' => $id,
                    'observacion' => "Producción de " . $orden['producto_nombre'] . " - Cantidad: $cantidad_producida"
                ]);

                // Registrar merma si hay diferencia
                if ($merma > 0) {
                    $this->registrarMerma([
                        'tipo' => 'produccion',
                        'ingrediente_id' => $ing['ingrediente_id'],
                        'cantidad' => $merma,
                        'unidad_medida_id' => $ing['unidad_medida_id'],
                        'costo_estimado' => $merma * ($ingrediente['costo_unitario'] ?? 0),
                        'fecha' => date('Y-m-d'),
                        'usuario_id' => $_SESSION['usuario_id'],
                        'orden_produccion_id' => $id,
                        'motivo' => 'Merma en producción',
                        'observacion' => "Diferencia entre cantidad teórica y real en orden " . $orden['numero']
                    ]);
                }
            }

            // Agregar producto terminado al stock
            $productoModel = new Producto();
            $producto = $productoModel->obtenerPorId($orden['producto_id']);
            $stock_anterior = $producto['stock'];
            $stock_nuevo = $stock_anterior + $cantidad_producida;

            $update = $this->pdo->prepare("UPDATE productos SET stock = ? WHERE id = ?");
            $update->execute([$stock_nuevo, $orden['producto_id']]);

            // Registrar movimiento de producto
            $movimientoModel->registrar([
                'tipo_movimiento' => 'entrada_produccion',
                'producto_id' => $orden['producto_id'],
                'cantidad' => $cantidad_producida,
                'stock_anterior' => $stock_anterior,
                'stock_nuevo' => $stock_nuevo,
                'motivo' => 'Ingreso por producción - Orden ' . $orden['numero'],
                'usuario_id' => $_SESSION['usuario_id'],
                'referencia_id' => $id,
                'observacion' => "Producción finalizada de " . $orden['producto_nombre']
            ]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function cancelar($id, $motivo = null) {
        $stmt = $this->pdo->prepare("UPDATE ordenes_produccion 
                                    SET estado = 'cancelada', observaciones = CONCAT(observaciones, ' - Cancelada: ', ?) 
                                    WHERE id = ?");
        return $stmt->execute([$motivo, $id]);
    }

    private function registrarMerma($datos) {
        $sql = "INSERT INTO mermas (tipo, producto_id, ingrediente_id, cantidad, unidad_medida_id, 
                                    costo_estimado, fecha, usuario_id, orden_produccion_id, motivo, observacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $datos['tipo'],
            $datos['producto_id'] ?? null,
            $datos['ingrediente_id'] ?? null,
            $datos['cantidad'],
            $datos['unidad_medida_id'],
            $datos['costo_estimado'] ?? 0,
            $datos['fecha'],
            $datos['usuario_id'],
            $datos['orden_produccion_id'] ?? null,
            $datos['motivo'] ?? null,
            $datos['observacion'] ?? null
        ]);
    }

    public function verificarStock($id) {
        $ingredientes = $this->obtenerIngredientes($id);
        $faltantes = [];
        
        foreach ($ingredientes as $ing) {
            if ($ing['cantidad_teorica'] > $ing['stock_actual']) {
                $faltantes[] = [
                    'ingrediente' => $ing['ingrediente_nombre'],
                    'necesario' => $ing['cantidad_teorica'],
                    'disponible' => $ing['stock_actual'],
                    'faltante' => $ing['cantidad_teorica'] - $ing['stock_actual'],
                    'unidad' => $ing['unidad_abreviatura']
                ];
            }
        }
        
        return $faltantes;
    }
}
?>