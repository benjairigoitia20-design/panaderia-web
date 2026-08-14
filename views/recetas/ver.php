<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h2><?= htmlspecialchars($receta['nombre']) ?></h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Producto:</strong> <?= htmlspecialchars($receta['producto_nombre'] ?? '-') ?></p>
                    <p><strong>Rendimiento:</strong> <?= $receta['rendimiento'] ?> <?= $receta['unidad_rendimiento'] ?></p>
                    <p><strong>Tiempo de preparación:</strong> <?= $receta['tiempo_preparacion'] ?> minutos</p>
                    <p><strong>Tiempo de cocción:</strong> <?= $receta['tiempo_coccion'] ?> minutos</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Costo total:</strong> $<?= number_format($receta['costo_total'], 2) ?></p>
                    <p><strong>Costo por unidad:</strong> $<?= number_format($receta['costo_por_unidad'], 2) ?></p>
                    <p><strong>Precio de venta:</strong> $<?= number_format($receta['producto_precio'] ?? 0, 2) ?></p>
                    <p><strong>Margen:</strong> 
                        <?php 
                        if (isset($receta['producto_precio']) && $receta['producto_precio'] > 0) {
                            $margen = (($receta['producto_precio'] - $receta['costo_por_unidad']) / $receta['producto_precio']) * 100;
                            echo number_format($margen, 2) . '%';
                        } else {
                            echo '-';
                        }
                        ?>
                    </p>
                </div>
            </div>
            
            <?php if (!empty($receta['instrucciones'])): ?>
                <div class="mt-3">
                    <h5>Instrucciones</h5>
                    <p><?= nl2br(htmlspecialchars($receta['instrucciones'])) ?></p>
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <h4>Ingredientes</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Ingrediente</th>
                                <th>Cantidad</th>
                                <th>Unidad</th>
                                <th>Costo Parcial</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ingredientes)): ?>
                                <tr><td colspan="4" class="text-center">No hay ingredientes registrados.</td></tr>
                            <?php else: ?>
                                <?php foreach ($ingredientes as $ing): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($ing['ingrediente_nombre']) ?></td>
                                        <td><?= number_format($ing['cantidad'], 2) ?></td>
                                        <td><?= htmlspecialchars($ing['unidad_abreviatura']) ?></td>
                                        <td>$<?= number_format($ing['costo_parcial'], 4) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="3" class="text-end">Costo Total:</th>
                                <th>$<?= number_format($receta['costo_total'], 2) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="index.php?modulo=recetas&accion=index" class="btn btn-secondary">Volver</a>
            <?php if (esAdmin() || tieneRol('panadero')): ?>
                <a href="index.php?modulo=recetas&accion=editar&id=<?= $receta['id'] ?>" class="btn btn-warning">Editar</a>
            <?php endif; ?>
        </div>
    </div>
</div>