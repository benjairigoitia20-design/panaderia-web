<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Ingrediente.php';
require_once __DIR__ . '/../models/MovimientoInventario.php';

class OrdenCompra {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function generarNumero() {
        $year = date('Y');
        $month = date('m');
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM ordenes_compra WHERE YEAR(fecha_orden) = $year AND MONTH(fecha_orden) = $month");
        $count = $stmt->fetchColumn() + 1;
        return 'OC-' . $year . $month . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function obtenerTodos($estado = null) {
        $sql = "SELECT oc.*, p.razon_social as proveedor_nombre, u.nombre as usuario_nombre
                FROM ordenes_compra oc
                LEFT JOIN proveedores p ON oc.proveedor_id = p.id
                LEFT JOIN usuarios u ON oc.usuario_id = u.id";
        
        if ($estado && $estado != 'todos') {
            $sql .= " WHERE oc.estado = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$estado]);
        } else {
            $sql .= " ORDER BY oc.fecha_orden DESC";
            $stmt = $this->pdo->query($sql);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT oc.*, p.razon_social as proveedor_nombre, p.telefono as proveedor_telefono,
                u.nombre as usuario_nombre
                FROM ordenes_compra oc
                LEFT JOIN proveedores p ON oc.proveedor_id = p.id
                LEFT JOIN usuarios u ON oc.usuario_id = u.id
                WHERE oc.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalles($orden_id) {
        $sql = "SELECT ocd.*, i.nombre as ingrediente_nombre, i.unidad_medida_id,
                u.nombre as unidad_nombre, u.abreviatura as unidad_abreviatura
                FROM orden_compra_detalles ocd
                LEFT JOIN ingredientes i ON ocd.ingrediente_id = i.id
                LEFT JOIN unidades_medida u ON i.unidad_medida_id = u.id
                WHERE ocd.orden_compra_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orden_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $this->pdo->beginTransaction();
        
        try {
            $numero = $this->generarNumero();
            
            $sql = "INSERT INTO ordenes_compra (numero, proveedor_id, usuario_id, fecha_orden, 
                    subtotal, descuento, total, estado, observaciones) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $numero,
                $datos['proveedor_id'],
                $_SESSION['usuario_id'],
                $datos['fecha_orden'],
                $datos['subtotal'],
                $datos['descuento'] ?? 0,
                $datos['total'],
                'pendiente',
                $datos['observaciones'] ?? null
            ]);

            if (!$result) {
                throw new Exception("Error al crear la orden de compra");
            }

            $orden_id = $this->pdo->lastInsertId();

            foreach ($datos['productos'] as $item) {
                $sql_det = "INSERT INTO orden_compra_detalles (orden_compra_id, ingrediente_id, cantidad, precio_unitario, subtotal) 
                            VALUES (?, ?, ?, ?, ?)";
                $stmt_det = $this->pdo->prepare($sql_det);
                $stmt_det->execute([
                    $orden_id,
                    $item['ingrediente_id'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['subtotal']
                ]);
            }

            $this->pdo->commit();
            return $orden_id;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function recibir($id, $datos) {
        $this->pdo->beginTransaction();
        
        try {
            $orden = $this->obtenerPorId($id);
            if (!$orden) {
                throw new Exception("Orden no encontrada");
            }

            if ($orden['estado'] == 'recibida') {
                throw new Exception("Esta orden ya fue recibida");
            }

            // Actualizar estado
            $sql = "UPDATE ordenes_compra SET estado = 'recibida', fecha_recepcion = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([date('Y-m-d'), $id]);

            $detalles = $this->obtenerDetalles($id);
            $ingredienteModel = new Ingrediente();
            $movimientoModel = new MovimientoInventario();

            foreach ($detalles as $detalle) {
                $cantidad_recibida = $datos['cantidades'][$detalle['id']] ?? $detalle['cantidad'];
                
                // Actualizar cantidad recibida
                $update = $this->pdo->prepare("UPDATE orden_compra_detalles SET cantidad_recibida = ? WHERE id = ?");
                $update->execute([$cantidad_recibida, $detalle['id']]);

                // Obtener ingrediente actual
                $ingrediente = $ingredienteModel->obtenerPorId($detalle['ingrediente_id']);
                $stock_anterior = $ingrediente['stock_actual'];
                $stock_nuevo = $stock_anterior + $cantidad_recibida;

                // Actualizar stock
                $ingredienteModel->actualizarStock($detalle['ingrediente_id'], $stock_nuevo);

                // Registrar movimiento
                $movimientoModel->registrar([
                    'tipo_movimiento' => 'entrada_compra',
                    'ingrediente_id' => $detalle['ingrediente_id'],
                    'cantidad' => $cantidad_recibida,
                    'stock_anterior' => $stock_anterior,
                    'stock_nuevo' => $stock_nuevo,
                    'motivo' => 'Compra - ' . $orden['numero'],
                    'usuario_id' => $_SESSION['usuario_id'],
                    'referencia_id' => $id,
                    'observacion' => "Recepción de orden de compra"
                ]);

                // Actualizar precio si cambió
                if ($detalle['precio_unitario'] != $ingrediente['costo_unitario']) {
                    $this->registrarHistorialPrecio(
                        $detalle['ingrediente_id'],
                        $ingrediente['costo_unitario'],
                        $detalle['precio_unitario'],
                        $id
                    );
                    
                    // Actualizar costo del ingrediente
                    $update_costo = $this->pdo->prepare("UPDATE ingredientes SET costo_unitario = ? WHERE id = ?");
                    $update_costo->execute([$detalle['precio_unitario'], $detalle['ingrediente_id']]);
                }
            }

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function cancelar($id, $motivo = null) {
        $stmt = $this->pdo->prepare("UPDATE ordenes_compra SET estado = 'cancelada', observaciones = CONCAT(observaciones, ' - Cancelada: ', ?) WHERE id = ?");
        return $stmt->execute([$motivo, $id]);
    }

    private function registrarHistorialPrecio($ingrediente_id, $precio_anterior, $precio_nuevo, $orden_id) {
        $sql = "INSERT INTO historial_precios (ingrediente_id, precio_anterior, precio_nuevo, orden_compra_id, usuario_id) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $ingrediente_id,
            $precio_anterior,
            $precio_nuevo,
            $orden_id,
            $_SESSION['usuario_id']
        ]);
    }

    public function obtenerEstadisticas() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'recibida' THEN 1 ELSE 0 END) as recibidas,
                    SUM(CASE WHEN estado = 'parcial' THEN 1 ELSE 0 END) as parciales,
                    SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) as canceladas,
                    SUM(total) as total_compras
                FROM ordenes_compra
                WHERE DATE(fecha_orden) = CURDATE()";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>