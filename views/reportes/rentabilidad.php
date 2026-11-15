<div class="container mt-4">
    <h2>Reporte de Rentabilidad</h2>

    <div class="row">
        <!-- Rentabilidad por Producto -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Rentabilidad por Producto</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio Venta</th>
                                    <th>Costo Prod.</th>
                                    <th>Ganancia</th>
                                    <th>Margen</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rentabilidad)): ?>
                                    <tr><td colspan="6" class="text-center">No hay datos</td></tr>
                                <?php else: ?>
                                    <?php foreach ($rentabilidad as $r): ?>
                                        <tr class="<?= $r['margen_porcentaje'] < 30 ? 'table-danger' : ($r['margen_porcentaje'] < 50 ? 'table-warning' : 'table-success') ?>">
                                            <td><?= htmlspecialchars($r['nombre']) ?></td>
                                            <td>$<?= number_format($r['precio_venta'], 2) ?></td>
                                            <td>$<?= number_format($r['costo_produccion'], 2) ?></td>
                                            <td>$<?= number_format($r['ganancia_unitaria'], 2) ?></td>
                                            <td><?= number_format($r['margen_porcentaje'], 1) ?>%</td>
                                            <td><?= number_format($r['stock']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rentabilidad por Categoría -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Rentabilidad por Categoría</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Categoría</th>
                                    <th>Margen</th>
                                    <th>Prod.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rentabilidad_categoria)): ?>
                                    <tr><td colspan="3" class="text-center">No hay datos</td></tr>
                                <?php else: ?>
                                    <?php foreach ($rentabilidad_categoria as $r): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($r['categoria']) ?></td>
                                            <td><?= number_format($r['margen_promedio'], 1) ?>%</td>
                                            <td><?= $r['cantidad_productos'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Resumen -->
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5>Resumen</h5>
                </div>
                <div class="card-body">
                    <?php
                    $total_productos = count($rentabilidad);
                    $productos_rentables = 0;
                    $productos_perdida = 0;
                    
                    foreach ($rentabilidad as $r) {
                        if ($r['margen_porcentaje'] > 0) {
                            $productos_rentables++;
                        } else {
                            $productos_perdida++;
                        }
                    }
                    ?>
                    <p><strong>Total Productos:</strong> <?= $total_productos ?></p>
                    <p><strong>Productos Rentables:</strong> <span class="text-success"><?= $productos_rentables ?></span></p>
                    <p><strong>Productos en Pérdida:</strong> <span class="text-danger"><?= $productos_perdida ?></span></p>
                    <hr>
                    <p><strong>Recomendación:</strong></p>
                    <?php if ($productos_perdida > 0): ?>
                        <p class="text-danger">⚠️ Revisa los productos en pérdida. Considera aumentar precios o reducir costos.</p>
                    <?php else: ?>
                        <p class="text-success">✅ Todos los productos son rentables.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>