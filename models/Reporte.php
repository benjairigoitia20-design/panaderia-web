<?php
require_once __DIR__ . '/../config/database.php';

class Reporte {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    // ============ REPORTE DE VENTAS ============
    
    public function ventasPorPeriodo($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    DATE(v.fecha) as fecha,
                    COUNT(*) as cantidad_ventas,
                    SUM(v.total) as total_ventas,
                    AVG(v.total) as promedio_venta,
                    SUM(v.subtotal) as subtotal_ventas,
                    SUM(v.descuento) as total_descuentos
                FROM ventas v
                WHERE DATE(v.fecha) BETWEEN ? AND ?
                    AND v.estado = 'completada'
                GROUP BY DATE(v.fecha)
                ORDER BY fecha DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ventasPorProducto($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    p.nombre as producto,
                    SUM(vd.cantidad) as cantidad_vendida,
                    SUM(vd.subtotal) as total_vendido,
                    COUNT(DISTINCT v.id) as numero_ventas,
                    AVG(vd.precio_unitario) as precio_promedio
                FROM venta_detalles vd
                JOIN ventas v ON vd.venta_id = v.id
                JOIN productos p ON vd.producto_id = p.id
                WHERE DATE(v.fecha) BETWEEN ? AND ?
                    AND v.estado = 'completada'
                GROUP BY vd.producto_id
                ORDER BY total_vendido DESC
                LIMIT 20";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ventasPorCategoria($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    COALESCE(c.nombre, 'Sin categoría') as categoria,
                    COUNT(DISTINCT v.id) as cantidad_ventas,
                    SUM(vd.cantidad) as cantidad_productos,
                    SUM(vd.subtotal) as total_vendido
                FROM venta_detalles vd
                JOIN ventas v ON vd.venta_id = v.id
                JOIN productos p ON vd.producto_id = p.id
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE DATE(v.fecha) BETWEEN ? AND ?
                    AND v.estado = 'completada'
                GROUP BY p.categoria_id
                ORDER BY total_vendido DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ventasPorMedioPago($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    medio_pago,
                    COUNT(*) as cantidad,
                    SUM(total) as total
                FROM ventas
                WHERE DATE(fecha) BETWEEN ? AND ?
                    AND estado = 'completada'
                GROUP BY medio_pago
                ORDER BY total DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============ REPORTE DE INVENTARIO ============
    
    public function stockProductos() {
        $sql = "SELECT 
                    p.id,
                    p.nombre,
                    p.stock,
                    p.stock_minimo,
                    p.precio,
                    p.estado,
                    c.nombre as categoria,
                    (p.stock * p.precio) as valor_stock
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                WHERE p.estado = 1
                ORDER BY p.stock ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function stockBajoProductos($umbral = null) {
        // Verificar si existe la columna stock_minimo
        try {
            $sql_check = "SHOW COLUMNS FROM productos LIKE 'stock_minimo'";
            $stmt_check = $this->pdo->query($sql_check);
            $existe_columna = $stmt_check->fetch() ? true : false;
        } catch (PDOException $e) {
            $existe_columna = false;
        }

        if ($existe_columna) {
            $sql = "SELECT 
                        p.id,
                        p.nombre,
                        p.stock,
                        p.stock_minimo,
                        p.precio,
                        c.nombre as categoria
                    FROM productos p
                    LEFT JOIN categorias c ON p.categoria_id = c.id
                    WHERE p.estado = 1 
                        AND p.stock <= p.stock_minimo
                    ORDER BY p.stock ASC";
        } else {
            $sql = "SELECT 
                        p.id,
                        p.nombre,
                        p.stock,
                        p.precio,
                        c.nombre as categoria,
                        10 as stock_minimo
                    FROM productos p
                    LEFT JOIN categorias c ON p.categoria_id = c.id
                    WHERE p.estado = 1 
                        AND p.stock <= 10
                    ORDER BY p.stock ASC";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function stockIngredientes() {
        $sql = "SELECT 
                    i.id,
                    i.nombre,
                    i.stock_actual,
                    i.stock_minimo,
                    i.costo_unitario,
                    i.categoria,
                    u.abreviatura as unidad,
                    (i.stock_actual * i.costo_unitario) as valor_stock
                FROM ingredientes i
                LEFT JOIN unidades_medida u ON i.unidad_medida_id = u.id
                WHERE i.estado = 1
                ORDER BY i.stock_actual ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function stockBajoIngredientes($umbral = null) {
        $sql = "SELECT 
                    i.id,
                    i.nombre,
                    i.stock_actual,
                    i.stock_minimo,
                    i.costo_unitario,
                    i.categoria,
                    u.abreviatura as unidad
                FROM ingredientes i
                LEFT JOIN unidades_medida u ON i.unidad_medida_id = u.id
                WHERE i.estado = 1 
                    AND i.stock_actual <= i.stock_minimo
                ORDER BY i.stock_actual ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function valorizacionInventario() {
        $sql_prod = "SELECT SUM(stock * precio) as total_productos FROM productos WHERE estado = 1";
        $stmt = $this->pdo->query($sql_prod);
        $total_productos = $stmt->fetch(PDO::FETCH_ASSOC)['total_productos'] ?? 0;

        $sql_ing = "SELECT SUM(stock_actual * costo_unitario) as total_ingredientes FROM ingredientes WHERE estado = 1";
        $stmt = $this->pdo->query($sql_ing);
        $total_ingredientes = $stmt->fetch(PDO::FETCH_ASSOC)['total_ingredientes'] ?? 0;

        return [
            'total_productos' => $total_productos,
            'total_ingredientes' => $total_ingredientes,
            'total_inventario' => $total_productos + $total_ingredientes
        ];
    }

    // ============ REPORTE DE PRODUCCIÓN ============
    
    public function produccionPorPeriodo($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    DATE(fecha_produccion) as fecha,
                    COUNT(*) as cantidad_ordenes,
                    SUM(cantidad_planificada) as planificado,
                    SUM(cantidad_producida) as producido,
                    AVG(cantidad_producida) as promedio_produccion,
                    SUM(costo_total) as costo_total
                FROM ordenes_produccion
                WHERE estado = 'terminada'
                    AND DATE(fecha_produccion) BETWEEN ? AND ?
                GROUP BY DATE(fecha_produccion)
                ORDER BY fecha DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function produccionPorProducto($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    p.nombre as producto,
                    COUNT(op.id) as cantidad_ordenes,
                    SUM(op.cantidad_planificada) as planificado,
                    SUM(op.cantidad_producida) as producido,
                    SUM(op.costo_total) as costo_total,
                    AVG(op.costo_total / NULLIF(op.cantidad_producida, 0)) as costo_promedio
                FROM ordenes_produccion op
                JOIN productos p ON op.producto_id = p.id
                WHERE op.estado = 'terminada'
                    AND DATE(op.fecha_produccion) BETWEEN ? AND ?
                GROUP BY op.producto_id
                ORDER BY producido DESC
                LIMIT 20";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============ REPORTE DE MERMAS ============
    
    public function mermasPorPeriodo($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    DATE(fecha) as fecha,
                    COUNT(*) as cantidad_mermas,
                    SUM(cantidad) as total_cantidad,
                    SUM(costo_estimado) as costo_total
                FROM mermas
                WHERE DATE(fecha) BETWEEN ? AND ?
                GROUP BY DATE(fecha)
                ORDER BY fecha DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function mermasPorMotivo($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    tipo as motivo,
                    COUNT(*) as cantidad,
                    SUM(cantidad) as total_cantidad,
                    SUM(costo_estimado) as costo_total,
                    AVG(costo_estimado) as costo_promedio
                FROM mermas
                WHERE DATE(fecha) BETWEEN ? AND ?
                GROUP BY tipo
                ORDER BY costo_total DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function mermasPorProducto($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    p.nombre as producto,
                    COUNT(m.id) as cantidad_mermas,
                    SUM(m.cantidad) as total_cantidad,
                    SUM(m.costo_estimado) as costo_total
                FROM mermas m
                LEFT JOIN productos p ON m.producto_id = p.id
                WHERE DATE(m.fecha) BETWEEN ? AND ?
                    AND m.producto_id IS NOT NULL
                GROUP BY m.producto_id
                ORDER BY costo_total DESC
                LIMIT 10";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function mermasPorIngrediente($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                    i.nombre as ingrediente,
                    COUNT(m.id) as cantidad_mermas,
                    SUM(m.cantidad) as total_cantidad,
                    SUM(m.costo_estimado) as costo_total
                FROM mermas m
                LEFT JOIN ingredientes i ON m.ingrediente_id = i.id
                WHERE DATE(m.fecha) BETWEEN ? AND ?
                    AND m.ingrediente_id IS NOT NULL
                GROUP BY m.ingrediente_id
                ORDER BY costo_total DESC
                LIMIT 10";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============ REPORTE DE RENTABILIDAD ============
    
    public function rentabilidadPorProducto() {
        $sql = "SELECT 
                    p.id,
                    p.nombre,
                    p.precio as precio_venta,
                    COALESCE(r.costo_por_unidad, 0) as costo_produccion,
                    (p.precio - COALESCE(r.costo_por_unidad, 0)) as ganancia_unitaria,
                    CASE 
                        WHEN p.precio > 0 THEN ((p.precio - COALESCE(r.costo_por_unidad, 0)) / p.precio * 100)
                        ELSE 0 
                    END as margen_porcentaje,
                    p.stock,
                    p.estado
                FROM productos p
                LEFT JOIN recetas r ON p.id = r.producto_id AND r.estado = 1
                WHERE p.estado = 1
                ORDER BY margen_porcentaje DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function rentabilidadPorCategoria() {
        $sql = "SELECT 
                    COALESCE(c.nombre, 'Sin categoría') as categoria,
                    COUNT(p.id) as cantidad_productos,
                    AVG(p.precio) as precio_promedio,
                    AVG(COALESCE(r.costo_por_unidad, 0)) as costo_promedio,
                    AVG(p.precio - COALESCE(r.costo_por_unidad, 0)) as ganancia_promedio,
                    CASE 
                        WHEN AVG(p.precio) > 0 THEN AVG((p.precio - COALESCE(r.costo_por_unidad, 0)) / p.precio * 100)
                        ELSE 0 
                    END as margen_promedio
                FROM productos p
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN recetas r ON p.id = r.producto_id AND r.estado = 1
                WHERE p.estado = 1
                GROUP BY p.categoria_id
                ORDER BY margen_promedio DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function resumenGeneral($fecha_inicio, $fecha_fin) {
        $sql_ventas = "SELECT 
                            COUNT(*) as total_ventas,
                            SUM(total) as total_ingresos,
                            SUM(subtotal) as subtotal,
                            SUM(descuento) as descuentos
                        FROM ventas
                        WHERE DATE(fecha) BETWEEN ? AND ?
                            AND estado = 'completada'";
        $stmt = $this->pdo->prepare($sql_ventas);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        $ventas = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql_prod = "SELECT 
                        COUNT(*) as total_ordenes,
                        SUM(cantidad_producida) as total_producido,
                        SUM(costo_total) as costo_produccion
                    FROM ordenes_produccion
                    WHERE estado = 'terminada'
                        AND DATE(fecha_produccion) BETWEEN ? AND ?";
        $stmt = $this->pdo->prepare($sql_prod);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        $produccion = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql_mermas = "SELECT 
                            COUNT(*) as total_mermas,
                            SUM(costo_estimado) as costo_mermas
                        FROM mermas
                        WHERE DATE(fecha) BETWEEN ? AND ?";
        $stmt = $this->pdo->prepare($sql_mermas);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        $mermas = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql_clientes = "SELECT COUNT(*) as nuevos_clientes 
                        FROM clientes 
                        WHERE DATE(created_at) BETWEEN ? AND ?";
        $stmt = $this->pdo->prepare($sql_clientes);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        $clientes = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql_pedidos = "SELECT 
                            COUNT(*) as total_pedidos,
                            SUM(total) as total_pedidos_ventas
                        FROM pedidos
                        WHERE DATE(fecha_pedido) BETWEEN ? AND ?";
        $stmt = $this->pdo->prepare($sql_pedidos);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        $pedidos = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'ventas' => $ventas,
            'produccion' => $produccion,
            'mermas' => $mermas,
            'clientes' => $clientes,
            'pedidos' => $pedidos,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ];
    }

    // ============ DASHBOARD DATOS ============
    
    public function datosDashboard() {
        $hoy = date('Y-m-d');
        $inicio_mes = date('Y-m-01');
        $inicio_semana = date('Y-m-d', strtotime('-7 days'));

        // Ventas del día
        $sql_ventas_dia = "SELECT SUM(total) as total FROM ventas WHERE DATE(fecha) = ? AND estado = 'completada'";
        $stmt = $this->pdo->prepare($sql_ventas_dia);
        $stmt->execute([$hoy]);
        $ventas_dia = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Ventas del mes
        $sql_ventas_mes = "SELECT SUM(total) as total FROM ventas WHERE DATE(fecha) BETWEEN ? AND ? AND estado = 'completada'";
        $stmt = $this->pdo->prepare($sql_ventas_mes);
        $stmt->execute([$inicio_mes, $hoy]);
        $ventas_mes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Ventas de la semana
        $sql_ventas_semana = "SELECT SUM(total) as total FROM ventas WHERE DATE(fecha) BETWEEN ? AND ? AND estado = 'completada'";
        $stmt = $this->pdo->prepare($sql_ventas_semana);
        $stmt->execute([$inicio_semana, $hoy]);
        $ventas_semana = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Pedidos pendientes
        $sql_pedidos = "SELECT COUNT(*) as total FROM pedidos WHERE estado = 'pendiente'";
        $stmt = $this->pdo->query($sql_pedidos);
        $pedidos_pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Verificar si existe columna stock_minimo en productos
        try {
            $sql_check = "SHOW COLUMNS FROM productos LIKE 'stock_minimo'";
            $stmt_check = $this->pdo->query($sql_check);
            $existe_stock_minimo = $stmt_check->fetch() ? true : false;
        } catch (PDOException $e) {
            $existe_stock_minimo = false;
        }

        // Productos con stock bajo
        if ($existe_stock_minimo) {
            $sql_stock = "SELECT COUNT(*) as total FROM productos WHERE estado = 1 AND stock <= stock_minimo";
        } else {
            $sql_stock = "SELECT COUNT(*) as total FROM productos WHERE estado = 1 AND stock <= 10";
        }
        $stmt = $this->pdo->query($sql_stock);
        $stock_bajo = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Ingredientes con stock bajo
        $sql_ing = "SELECT COUNT(*) as total FROM ingredientes WHERE estado = 1 AND stock_actual <= stock_minimo";
        $stmt = $this->pdo->query($sql_ing);
        $ingredientes_bajo = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Producción del día
        $sql_prod = "SELECT SUM(cantidad_producida) as total FROM ordenes_produccion WHERE estado = 'terminada' AND DATE(fecha_produccion) = ?";
        $stmt = $this->pdo->prepare($sql_prod);
        $stmt->execute([$hoy]);
        $produccion_dia = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Mermas del día
        $sql_mermas = "SELECT SUM(costo_estimado) as total FROM mermas WHERE DATE(fecha) = ?";
        $stmt = $this->pdo->prepare($sql_mermas);
        $stmt->execute([$hoy]);
        $mermas_dia = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Caja actual
        $sql_caja = "SELECT monto_contado FROM caja WHERE estado = 'abierta' ORDER BY id DESC LIMIT 1";
        $stmt = $this->pdo->query($sql_caja);
        $caja = $stmt->fetch(PDO::FETCH_ASSOC);
        $caja_actual = $caja['monto_contado'] ?? 0;

        return [
            'ventas_dia' => $ventas_dia,
            'ventas_mes' => $ventas_mes,
            'ventas_semana' => $ventas_semana,
            'pedidos_pendientes' => $pedidos_pendientes,
            'stock_bajo' => $stock_bajo,
            'ingredientes_bajo' => $ingredientes_bajo,
            'produccion_dia' => $produccion_dia,
            'mermas_dia' => $mermas_dia,
            'caja_actual' => $caja_actual
        ];
    }

    // ============================================================
    // NUEVO MÉTODO PARA ÚLTIMAS ACTIVIDADES (AGREGADO AL FINAL)
    // ============================================================
    
    public function obtenerUltimasActividades($limite = 10) {
        try {
            $sql = "SELECT 
                        DATE_FORMAT(a.created_at, '%d/%m/%Y %H:%i') as fecha,
                        u.nombre as usuario,
                        a.accion,
                        a.modulo
                    FROM auditoria a
                    LEFT JOIN usuarios u ON a.usuario_id = u.id
                    ORDER BY a.created_at DESC
                    LIMIT ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$limite]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

?>