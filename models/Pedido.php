<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/MovimientoInventario.php';

class Pedido {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function generarNumero() {
        $year = date('Y');
        $month = date('m');
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM pedidos WHERE YEAR(fecha_pedido) = $year AND MONTH(fecha_pedido) = $month");
        $count = $stmt->fetchColumn() + 1;
        return 'PED-' . $year . $month . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function obtenerTodos($estado = null) {
        $sql = "SELECT p.*, c.nombre as cliente_nombre, c.apellido as cliente_apellido,
                c.telefono as cliente_telefono,
                u.nombre as usuario_nombre
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN usuarios u ON p.usuario_id = u.id";
        
        if ($estado && $estado != 'todos') {
            $sql .= " WHERE p.estado = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$estado]);
        } else {
            $sql .= " ORDER BY p.fecha_entrega ASC, p.fecha_pedido DESC";
            $stmt = $this->pdo->query($sql);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT p.*, c.nombre as cliente_nombre, c.apellido as cliente_apellido,
                c.telefono as cliente_telefono, c.email as cliente_email,
                u.nombre as usuario_nombre
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN usuarios u ON p.usuario_id = u.id
                WHERE p.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalles($pedido_id) {
        $sql = "SELECT pd.*, pr.nombre as producto_nombre, pr.imagen
                FROM pedido_detalles pd
                LEFT JOIN productos pr ON pd.producto_id = pr.id
                WHERE pd.pedido_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerSeguimiento($pedido_id) {
        $sql = "SELECT ps.*, u.nombre as usuario_nombre
                FROM pedido_seguimiento ps
                LEFT JOIN usuarios u ON ps.usuario_id = u.id
                WHERE ps.pedido_id = ?
                ORDER BY ps.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorFecha($fecha) {
        $sql = "SELECT p.*, c.nombre as cliente_nombre, c.apellido as cliente_apellido,
                c.telefono as cliente_telefono
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                WHERE p.fecha_entrega = ? AND p.estado != 'cancelado' AND p.estado != 'entregado'
                ORDER BY p.hora_entrega ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $this->pdo->beginTransaction();
        
        try {
            $numero = $this->generarNumero();
            $saldo = $datos['total'] - ($datos['senia'] ?? 0);
            
            // Insertar pedido
            $sql = "INSERT INTO pedidos (numero, cliente_id, usuario_id, fecha_entrega, hora_entrega, 
                    subtotal, descuento, total, senia, saldo, estado, observaciones) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $numero,
                $datos['cliente_id'],
                $_SESSION['usuario_id'],
                $datos['fecha_entrega'],
                $datos['hora_entrega'] ?? null,
                $datos['subtotal'],
                $datos['descuento'] ?? 0,
                $datos['total'],
                $datos['senia'] ?? 0,
                $saldo,
                'pendiente',
                $datos['observaciones'] ?? null
            ]);

            if (!$result) {
                throw new Exception("Error al crear el pedido");
            }

            $pedido_id = $this->pdo->lastInsertId();

            // Insertar detalles
            foreach ($datos['productos'] as $item) {
                $sql_det = "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario, subtotal, observaciones) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_det = $this->pdo->prepare($sql_det);
                $stmt_det->execute([
                    $pedido_id,
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['subtotal'],
                    $item['observaciones'] ?? null
                ]);
            }

            // Registrar seguimiento
            $this->registrarSeguimiento($pedido_id, 'pendiente', 'Pedido creado');

            $this->pdo->commit();
            return $pedido_id;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function actualizarEstado($id, $estado, $observacion = null) {
        $this->pdo->beginTransaction();
        
        try {
            $pedido = $this->obtenerPorId($id);
            if (!$pedido) {
                throw new Exception("Pedido no encontrado");
            }

            // Si el pedido ya está entregado o cancelado, no se puede modificar
            if (in_array($pedido['estado'], ['entregado', 'cancelado'])) {
                throw new Exception("No se puede modificar un pedido entregado o cancelado");
            }

            // Si pasamos a confirmado, podemos crear orden de producción automática
            if ($estado == 'confirmado' && $pedido['estado'] != 'confirmado') {
                // Verificar stock y crear orden de producción si es necesario
                $this->crearOrdenProduccion($id);
            }

            // Actualizar estado
            $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$estado, $id]);

            // Registrar seguimiento
            $this->registrarSeguimiento($id, $estado, $observacion);

            // Si se entrega, descontar stock
            if ($estado == 'entregado') {
                $this->descontarStockPedido($id);
            }

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function registrarSeguimiento($pedido_id, $estado, $observacion = null) {
        $sql = "INSERT INTO pedido_seguimiento (pedido_id, estado, usuario_id, observacion) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $pedido_id,
            $estado,
            $_SESSION['usuario_id'],
            $observacion
        ]);
    }

    public function registrarPago($id, $monto) {
        $this->pdo->beginTransaction();
        
        try {
            $pedido = $this->obtenerPorId($id);
            if (!$pedido) {
                throw new Exception("Pedido no encontrado");
            }

            if ($pedido['saldo'] <= 0) {
                throw new Exception("El pedido ya está saldado");
            }

            $nuevo_saldo = max(0, $pedido['saldo'] - $monto);
            $nueva_senia = $pedido['senia'] + $monto;

            $sql = "UPDATE pedidos SET senia = ?, saldo = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nueva_senia, $nuevo_saldo, $id]);

            // Registrar seguimiento
            $this->registrarSeguimiento($id, $pedido['estado'], "Pago registrado: $$monto. Saldo pendiente: $$nuevo_saldo");

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function crearOrdenProduccion($pedido_id) {
        // Esta función debería crear una orden de producción automática
        // Por ahora solo registramos en seguimiento
        $this->registrarSeguimiento($pedido_id, 'confirmado', 'Pedido confirmado - Se debe programar producción');
        
        // Aquí podrías llamar al modelo de OrdenProduccion para crear una orden automática
        // Por simplicidad, dejamos que el usuario cree la orden manualmente desde Producción
    }

    private function descontarStockPedido($pedido_id) {
        $detalles = $this->obtenerDetalles($pedido_id);
        $productoModel = new Producto();
        $movimientoModel = new MovimientoInventario();

        foreach ($detalles as $detalle) {
            $producto = $productoModel->obtenerPorId($detalle['producto_id']);
            $stock_anterior = $producto['stock'];
            $stock_nuevo = $stock_anterior - $detalle['cantidad'];

            if ($stock_nuevo < 0) {
                throw new Exception("Stock insuficiente para el producto: " . $producto['nombre']);
            }

            $update = $this->pdo->prepare("UPDATE productos SET stock = ? WHERE id = ?");
            $update->execute([$stock_nuevo, $detalle['producto_id']]);

            $movimientoModel->registrar([
                'tipo_movimiento' => 'salida_venta',
                'producto_id' => $detalle['producto_id'],
                'cantidad' => $detalle['cantidad'],
                'stock_anterior' => $stock_anterior,
                'stock_nuevo' => $stock_nuevo,
                'motivo' => 'Entrega de pedido - ' . $pedido_id,
                'usuario_id' => $_SESSION['usuario_id'],
                'referencia_id' => $pedido_id,
                'observacion' => "Entrega de pedido #$pedido_id"
            ]);
        }
    }

    public function cancelar($id, $motivo = null) {
        return $this->actualizarEstado($id, 'cancelado', $motivo);
    }

    public function obtenerEstadisticas() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'confirmado' THEN 1 ELSE 0 END) as confirmados,
                    SUM(CASE WHEN estado = 'en_produccion' THEN 1 ELSE 0 END) as en_produccion,
                    SUM(CASE WHEN estado = 'listo' THEN 1 ELSE 0 END) as listos,
                    SUM(CASE WHEN estado = 'entregado' THEN 1 ELSE 0 END) as entregados,
                    SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                    SUM(total) as total_ventas,
                    SUM(senia) as total_senias,
                    SUM(saldo) as total_saldos
                FROM pedidos
                WHERE DATE(fecha_pedido) = CURDATE()";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerEntregasHoy() {
        $hoy = date('Y-m-d');
        return $this->obtenerPorFecha($hoy);
    }

    public function obtenerEntregasManana() {
        $manana = date('Y-m-d', strtotime('+1 day'));
        return $this->obtenerPorFecha($manana);
    }
}
?>