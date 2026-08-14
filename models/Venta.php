<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/MovimientoInventario.php';

class Venta {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    public function generarNumero() {
        $year = date('Y');
        $month = date('m');
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM ventas WHERE YEAR(fecha) = $year AND MONTH(fecha) = $month");
        $count = $stmt->fetchColumn() + 1;
        return 'VTA-' . $year . $month . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function crear($datos) {
        $this->pdo->beginTransaction();
        
        try {
            $numero = $this->generarNumero();
            
            // Insertar venta
            $sql = "INSERT INTO ventas (numero, cliente_id, usuario_id, subtotal, descuento, total, medio_pago, observaciones) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $numero,
                $datos['cliente_id'] ?? null,
                $_SESSION['usuario_id'],
                $datos['subtotal'],
                $datos['descuento'] ?? 0,
                $datos['total'],
                $datos['medio_pago'],
                $datos['observaciones'] ?? null
            ]);

            if (!$result) {
                throw new Exception("Error al crear la venta");
            }

            $venta_id = $this->pdo->lastInsertId();
            $productoModel = new Producto();
            $movimientoModel = new MovimientoInventario();

            // Insertar detalles y actualizar stock
            foreach ($datos['productos'] as $item) {
                // Insertar detalle
                $sql_det = "INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio_unitario, subtotal) 
                            VALUES (?, ?, ?, ?, ?)";
                $stmt_det = $this->pdo->prepare($sql_det);
                $stmt_det->execute([
                    $venta_id,
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['subtotal']
                ]);

                // Actualizar stock del producto
                $producto = $productoModel->obtenerPorId($item['producto_id']);
                $stock_anterior = $producto['stock'];
                $stock_nuevo = $stock_anterior - $item['cantidad'];

                if ($stock_nuevo < 0) {
                    throw new Exception("Stock insuficiente para el producto: " . $producto['nombre']);
                }

                $update = $this->pdo->prepare("UPDATE productos SET stock = ? WHERE id = ?");
                $update->execute([$stock_nuevo, $item['producto_id']]);

                // Registrar movimiento de inventario
                $movimientoModel->registrar([
                    'tipo_movimiento' => 'salida_venta',
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'stock_anterior' => $stock_anterior,
                    'stock_nuevo' => $stock_nuevo,
                    'motivo' => 'Venta - ' . $numero,
                    'usuario_id' => $_SESSION['usuario_id'],
                    'referencia_id' => $venta_id,
                    'observacion' => "Venta de " . $item['cantidad'] . " unidades de " . $producto['nombre']
                ]);
            }

            // Registrar en caja (si está abierta)
            $caja_abierta = $this->obtenerCajaAbierta();
            if ($caja_abierta) {
                $sql_caja = "INSERT INTO caja_movimientos (caja_id, tipo, monto, descripcion, venta_id, usuario_id) 
                              VALUES (?, 'venta', ?, ?, ?, ?)";
                $stmt_caja = $this->pdo->prepare($sql_caja);
                $stmt_caja->execute([
                    $caja_abierta['id'],
                    $datos['total'],
                    'Venta ' . $numero,
                    $venta_id,
                    $_SESSION['usuario_id']
                ]);
            }

            $this->pdo->commit();
            return $venta_id;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function obtenerPorId($id) {
        $sql = "SELECT v.*, c.nombre as cliente_nombre, c.apellido as cliente_apellido,
                u.nombre as usuario_nombre
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                WHERE v.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalles($venta_id) {
        $sql = "SELECT vd.*, p.nombre as producto_nombre, p.imagen
                FROM venta_detalles vd
                LEFT JOIN productos p ON vd.producto_id = p.id
                WHERE vd.venta_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$venta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodas($limite = 100) {
        $sql = "SELECT v.*, c.nombre as cliente_nombre, c.apellido as cliente_apellido,
                u.nombre as usuario_nombre
                FROM ventas v
                LEFT JOIN clientes c ON v.cliente_id = c.id
                LEFT JOIN usuarios u ON v.usuario_id = u.id
                WHERE v.estado = 'completada'
                ORDER BY v.fecha DESC
                LIMIT " . intval($limite);
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerVentasDelDia() {
        $sql = "SELECT * FROM ventas WHERE DATE(fecha) = CURDATE() AND estado = 'completada'";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTotalDelDia() {
        $sql = "SELECT SUM(total) as total FROM ventas WHERE DATE(fecha) = CURDATE() AND estado = 'completada'";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function obtenerCajaAbierta() {
        $sql = "SELECT * FROM caja WHERE estado = 'abierta' ORDER BY id DESC LIMIT 1";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cancelar($id) {
        $this->pdo->beginTransaction();
        
        try {
            $venta = $this->obtenerPorId($id);
            if (!$venta) {
                throw new Exception("Venta no encontrada");
            }

            // Cambiar estado de la venta
            $stmt = $this->pdo->prepare("UPDATE ventas SET estado = 'cancelada' WHERE id = ?");
            $stmt->execute([$id]);

            // Devolver stock
            $detalles = $this->obtenerDetalles($id);
            $productoModel = new Producto();
            $movimientoModel = new MovimientoInventario();

            foreach ($detalles as $detalle) {
                $producto = $productoModel->obtenerPorId($detalle['producto_id']);
                $stock_anterior = $producto['stock'];
                $stock_nuevo = $stock_anterior + $detalle['cantidad'];

                $update = $this->pdo->prepare("UPDATE productos SET stock = ? WHERE id = ?");
                $update->execute([$stock_nuevo, $detalle['producto_id']]);

                $movimientoModel->registrar([
                    'tipo_movimiento' => 'devolucion',
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'stock_anterior' => $stock_anterior,
                    'stock_nuevo' => $stock_nuevo,
                    'motivo' => 'Cancelación de venta - ' . $venta['numero'],
                    'usuario_id' => $_SESSION['usuario_id'],
                    'referencia_id' => $id,
                    'observacion' => "Devolución por cancelación de venta"
                ]);
            }

            // Eliminar movimiento de caja
            $stmt = $this->pdo->prepare("DELETE FROM caja_movimientos WHERE venta_id = ?");
            $stmt->execute([$id]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
?>