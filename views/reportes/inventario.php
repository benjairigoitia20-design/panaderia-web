<div class="container mt-4">
    <h2>Reporte de Inventario</h2>
    
    <!-- Valorización -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Valor Productos</h6>
                    <h4>$<?= number_format($valorizacion['total_productos'], 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Valor Ingredientes</h6>
                    <h4>$<?= number_format($valorizacion['total_ingredientes'], 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Valor Total Inventario</h6>
                    <h4>$<?= number_format($valorizacion['total_inventario'], 2) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Bajo -->
    <?php if (!empty($stock_bajo_productos) || !empty($stock_bajo_ingredientes)): ?>
        <div class="alert alert-danger">
            <h5>⚠️ Alertas de Stock Bajo</h5>
            <?php if (!empty($stock_bajo_productos)): ?>
                <p><strong>Productos:</strong> <?= implode(', ', array_column($stock_bajo_productos, 'nombre')) ?></p>
            <?php endif; ?>
            <?php if (!empty($stock_bajo_ingredientes)): ?>
                <p><strong>Ingredientes:</strong> <?= implode(', ', array_column($stock_bajo_ingredientes, 'nombre')) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Productos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Stock de Productos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Mínimo</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $p): ?>
                                    <tr class="<?= $p['stock'] <= $p['stock_minimo'] ? 'table-danger' : '' ?>">
                                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                                        <td><?= number_format($p['stock']) ?></td>
                                        <td><?= number_format($p['stock_minimo']) ?></td>
                                        <td>$<?= number_format($p['valor_stock'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingredientes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Stock de Ingredientes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ingrediente</th>
                                    <th>Stock</th>
                                    <th>Mínimo</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ingredientes as $i): ?>
                                    <tr class="<?= $i['stock_actual'] <= $i['stock_minimo'] ? 'table-danger' : '' ?>">
                                        <td><?= htmlspecialchars($i['nombre']) ?></td>
                                        <td><?= number_format($i['stock_actual'], 2) ?> <?= $i['unidad'] ?></td>
                                        <td><?= number_format($i['stock_minimo'], 2) ?> <?= $i['unidad'] ?></td>
                                        <td>$<?= number_format($i['valor_stock'], 2) ?></td>
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