<div class="container mt-4">
    <h2>Reporte de Ventas</h2>
    
    <!-- Filtros -->
    <form method="GET" class="row g-3 mb-4">
        <input type="hidden" name="modulo" value="reportes">
        <input type="hidden" name="accion" value="ventas">
        <div class="col-md-3">
            <label class="form-label">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" value="<?= $fecha_inicio ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Fecha Fin</label>
            <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
        <div class="col-md-4 d-flex align-items-end justify-content-end">
            <a href="index.php?modulo=reportes&accion=ventas&fecha_inicio=<?= date('Y-m-01') ?>&fecha_fin=<?= date('Y-m-d') ?>" class="btn btn-outline-secondary me-2">Este Mes</a>
            <a href="index.php?modulo=reportes&accion=ventas&fecha_inicio=<?= date('Y-m-d', strtotime('-7 days')) ?>&fecha_fin=<?= date('Y-m-d') ?>" class="btn btn-outline-secondary">Última Semana</a>
        </div>
    </form>

    <!-- Resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Total Ventas</h6>
                    <h4>$<?= number_format($resumen['ventas']['total_ingresos'] ?? 0, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Cantidad Ventas</h6>
                    <h4><?= $resumen['ventas']['total_ventas'] ?? 0 ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Descuentos</h6>
                    <h4>$<?= number_format($resumen['ventas']['descuentos'] ?? 0, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Promedio por Venta</h6>
                    <h4>$<?= number_format(($resumen['ventas']['total_ventas'] ?? 0) > 0 ? ($resumen['ventas']['total_ingresos'] ?? 0) / ($resumen['ventas']['total_ventas'] ?? 1) : 0, 2) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Ventas por día -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Ventas por Día</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                    <th>Promedio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas_periodo as $v): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($v['fecha'])) ?></td>
                                        <td><?= $v['cantidad_ventas'] ?></td>
                                        <td>$<?= number_format($v['total_ventas'], 2) ?></td>
                                        <td>$<?= number_format($v['promedio_venta'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventas por producto -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Productos Más Vendidos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas_productos as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['producto']) ?></td>
                                        <td><?= number_format($p['cantidad_vendida']) ?></td>
                                        <td>$<?= number_format($p['total_vendido'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Ventas por categoría -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Ventas por Categoría</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Categoría</th>
                                    <th>Ventas</th>
                                    <th>Productos</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas_categorias as $c): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($c['categoria']) ?></td>
                                        <td><?= $c['cantidad_ventas'] ?></td>
                                        <td><?= number_format($c['cantidad_productos']) ?></td>
                                        <td>$<?= number_format($c['total_vendido'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventas por medio de pago -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Medios de Pago</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Medio</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_general = array_sum(array_column($ventas_pagos, 'total'));
                                foreach ($ventas_pagos as $p): 
                                ?>
                                    <tr>
                                        <td><?= ucfirst($p['medio_pago']) ?></td>
                                        <td><?= $p['cantidad'] ?></td>
                                        <td>$<?= number_format($p['total'], 2) ?></td>
                                        <td><?= $total_general > 0 ? number_format(($p['total'] / $total_general) * 100, 1) : 0 ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>