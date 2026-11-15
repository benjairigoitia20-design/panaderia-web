<div class="container mt-4">
    <h2>Reporte de Producción</h2>
    
    <!-- Filtros -->
    <form method="GET" class="row g-3 mb-4">
        <input type="hidden" name="modulo" value="reportes">
        <input type="hidden" name="accion" value="produccion">
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
    </form>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Producción por Día</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Órdenes</th>
                                    <th>Planificado</th>
                                    <th>Producido</th>
                                    <th>Costo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($produccion_periodo)): ?>
                                    <tr><td colspan="5" class="text-center">No hay datos</td></tr>
                                <?php else: ?>
                                    <?php foreach ($produccion_periodo as $p): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($p['fecha'])) ?></td>
                                            <td><?= $p['cantidad_ordenes'] ?></td>
                                            <td><?= number_format($p['planificado']) ?></td>
                                            <td><?= number_format($p['producido']) ?></td>
                                            <td>$<?= number_format($p['costo_total'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Producción por Producto</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Producido</th>
                                    <th>Costo Total</th>
                                    <th>Costo Prom.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($produccion_productos)): ?>
                                    <tr><td colspan="4" class="text-center">No hay datos</td></tr>
                                <?php else: ?>
                                    <?php foreach ($produccion_productos as $p): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['producto']) ?></td>
                                            <td><?= number_format($p['producido']) ?></td>
                                            <td>$<?= number_format($p['costo_total'], 2) ?></td>
                                            <td>$<?= number_format($p['costo_promedio'] ?? 0, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>