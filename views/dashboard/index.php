<?php
$esUsuarioPanadero = tieneRol('panadero');

if ($esUsuarioPanadero) {
    $pdo = conectarDB();

    $ventasHoy = $pdo->query("SELECT COALESCE(SUM(total), 0) AS total, COUNT(*) AS cantidad FROM ventas WHERE DATE(fecha) = CURDATE() AND estado = 'completada'")->fetch(PDO::FETCH_ASSOC);
    $ventasMes = $pdo->query("SELECT COALESCE(SUM(total), 0) AS total FROM ventas WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE()) AND estado = 'completada'")->fetch(PDO::FETCH_ASSOC);
    $productosTop = $pdo->query("SELECT p.nombre, SUM(vd.cantidad) AS cantidad, SUM(vd.subtotal) AS subtotal
        FROM venta_detalles vd
        INNER JOIN productos p ON p.id = vd.producto_id
        INNER JOIN ventas v ON v.id = vd.venta_id
        WHERE DATE(v.fecha) = CURDATE() AND v.estado = 'completada'
        GROUP BY p.id, p.nombre
        ORDER BY cantidad DESC
        LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);

    $ticketPromedio = ($ventasHoy['cantidad'] > 0) ? $ventasHoy['total'] / $ventasHoy['cantidad'] : 0;
?>
<div class="animate-in">
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card animate-in">
                <div class="stat-icon success"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-value">$<?= number_format($ventasHoy['total'] ?? 0, 2, ',', '.') ?></div>
                <div class="stat-label">Ventas hoy</div>
                <div class="stat-change positive"><?= (int)($ventasHoy['cantidad'] ?? 0) ?> ventas</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card animate-in">
                <div class="stat-icon primary"><i class="bi bi-calendar3"></i></div>
                <div class="stat-value">$<?= number_format($ventasMes['total'] ?? 0, 2, ',', '.') ?></div>
                <div class="stat-label">Ventas del mes</div>
                <div class="stat-change neutral">Periodo actual</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card animate-in">
                <div class="stat-icon warning"><i class="bi bi-receipt"></i></div>
                <div class="stat-value">$<?= number_format($ticketPromedio, 2, ',', '.') ?></div>
                <div class="stat-label">Ticket promedio</div>
                <div class="stat-change neutral">Por venta</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card animate-in">
                <div class="stat-icon info"><i class="bi bi-box-seam"></i></div>
                <div class="stat-value"><?= count($productosTop) ?></div>
                <div class="stat-label">Productos activos</div>
                <div class="stat-change neutral">Más vendidos hoy</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="chart-card animate-in h-100">
                <div class="chart-header">
                    <h6><i class="bi bi-star-fill text-warning"></i> Productos más vendidos hoy</h6>
                    <span class="chart-tag">Top 4</span>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($productosTop)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Todavía no hay ventas registradas hoy.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($productosTop as $producto): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                        <td><span class="badge bg-success"><?= (int)$producto['cantidad'] ?> uds.</span></td>
                                        <td>$<?= number_format($producto['subtotal'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-card animate-in h-100">
                <div class="chart-header">
                    <h6><i class="bi bi-lightning-charge text-success"></i> Acciones rápidas</h6>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <a href="index.php?modulo=ventas&accion=nueva" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Nueva venta
                    </a>
                    <a href="index.php?modulo=ventas&accion=index" class="btn btn-outline-primary">
                        <i class="bi bi-list-ul"></i> Ver ventas
                    </a>
                    <a href="index.php?modulo=productos&accion=index" class="btn btn-outline-secondary">
                        <i class="bi bi-box"></i> Ver productos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } else { ?>
<div class="animate-in">
    <div class="stats-grid">
        <div class="stat-card animate-in">
            <div class="stat-icon primary"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value">$0,00</div>
            <div class="stat-label">Ventas del Día</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>

        <div class="stat-card animate-in">
            <div class="stat-icon success"><i class="bi bi-calendar3"></i></div>
            <div class="stat-value">$0,00</div>
            <div class="stat-label">Ventas del Mes</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>

        <div class="stat-card animate-in">
            <div class="stat-icon warning"><i class="bi bi-clock-history"></i></div>
            <div class="stat-value">0</div>
            <div class="stat-label">Pedidos Pendientes</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>

        <div class="stat-card animate-in">
            <div class="stat-icon danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="stat-value">0</div>
            <div class="stat-label">Stock Bajo</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>

        <div class="stat-card animate-in">
            <div class="stat-icon info"><i class="bi bi-gear"></i></div>
            <div class="stat-value">0</div>
            <div class="stat-label">Producción del Día</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>

        <div class="stat-card animate-in">
            <div class="stat-icon secondary"><i class="bi bi-wallet2"></i></div>
            <div class="stat-value">$0,00</div>
            <div class="stat-label">Caja Actual</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card animate-in">
            <div class="chart-header">
                <h6><i class="bi bi-graph-up-arrow text-primary"></i> Ventas de la Semana</h6>
                <span class="chart-tag">Últimos 7 días</span>
            </div>
            <canvas id="ventasChart" height="200"></canvas>
        </div>

        <div class="chart-card animate-in">
            <div class="chart-header">
                <h6><i class="bi bi-pie-chart text-success"></i> Ventas por Categoría</h6>
                <span class="chart-tag">Distribución</span>
            </div>
            <canvas id="categoriasChart" height="200"></canvas>
        </div>
    </div>

    <div class="quick-actions">
        <a href="index.php?modulo=productos&accion=crear" class="btn btn-secondary">
            <i class="bi bi-plus-circle"></i> Nuevo Producto
        </a>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="chart-card animate-in">
                <div class="chart-header">
                    <h6><i class="bi bi-clock text-secondary"></i> Últimas Actividades</h6>
                    <span class="chart-tag">Actividad reciente</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Módulo</th>
                            </tr>
                        </thead>
                        <tbody id="ultimasActividades">
                            <tr>
                                <td colspan="4" class="text-center text-muted">Cargando actividades...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/dashboard.js"></script>
<?php } ?>