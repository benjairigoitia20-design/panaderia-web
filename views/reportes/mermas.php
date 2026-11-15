<div class="container mt-4">
    <h2>Reporte de Mermas</h2>
    
    <!-- Filtros -->
    <form method="GET" class="row g-3 mb-4">
        <input type="hidden" name="modulo" value="reportes">
        <input type="hidden" name="accion" value="mermas">
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
                    <h5>Mermas por Día</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cantidad</th>
                                    <th>Costo Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($mermas_periodo)): ?>
                                    <tr><td colspan="3" class="text-center">No hay datos</td></tr>
                                <?php else: ?>
                                    <?php foreach ($mermas_periodo as $m): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($m['fecha'])) ?></td>
                                            <td><?= number_format($m['total_cantidad'], 2) ?></td>
                                            <td>$<?= number_format($m['costo_total'], 2) ?></td>
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
                    <h5>Mermas por Motivo</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Motivo</th>
                                    <th>Cantidad</th>
                                    <th>Costo Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($mermas_motivos)): ?>
                                    <tr><td colspan="3" class="text-center">No hay datos</td></tr>
                                <?php else: ?>
                                    <?php foreach ($mermas_motivos as $m): ?>
                                        <tr>
                                            <td><?= ucfirst(str_replace('_', ' ', $m['motivo'])) ?></td>
                                            <td><?= number_format($m['total_cantidad'], 2) ?></td>
                                            <td>$<?= number_format($m['costo_total'], 2) ?></td>
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

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Mermas por Producto</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Costo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($mermas_productos)): ?>
                                    <tr><td colspan="3" class="text-center">No hay datos</td></tr>
                                <?php else: ?>
                                    <?php foreach ($mermas_productos as $m): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($m['producto']) ?></td>
                                            <td><?= number_format($m['total_cantidad'], 2) ?></td>
                                            <td>$<?= number_format($m['costo_total'], 2) ?></td>
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
                    <h5>Mermas por Ingrediente</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ingrediente</th>
                                    <th>Cantidad</th>
                                    <th>Costo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($mermas_ingredientes)): ?>
                                    <tr><td colspan="3" class="text-center">No hay datos</td></tr>
                                <?php else: ?>
                                    <?php foreach ($mermas_ingredientes as $m): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($m['ingrediente']) ?></td>
                                            <td><?= number_format($m['total_cantidad'], 2) ?></td>
                                            <td>$<?= number_format($m['costo_total'], 2) ?></td>
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