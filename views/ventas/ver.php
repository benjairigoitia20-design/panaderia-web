<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Venta <?= htmlspecialchars($venta['numero']) ?></h2>
            <span class="badge <?= $venta['estado'] == 'completada' ? 'bg-success' : 'bg-danger' ?>">
                <?= ucfirst($venta['estado']) ?>
            </span>
        </div>
        <div class="card-body">
            <?php if ($mensaje = getMensaje()): ?>
                <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></p>
                    <p><strong>Usuario:</strong> <?= htmlspecialchars($venta['usuario_nombre']) ?></p>
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($venta['cliente_nombre'] ?? '') ?> <?= htmlspecialchars($venta['cliente_apellido'] ?? '') ?></p>
                    <p><strong>Medio de pago:</strong> <?= ucfirst($venta['medio_pago']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Subtotal:</strong> $<?= number_format($venta['subtotal'], 2) ?></p>
                    <p><strong>Descuento:</strong> $<?= number_format($venta['descuento'], 2) ?></p>
                    <p><strong>Total:</strong> <strong class="text-success">$<?= number_format($venta['total'], 2) ?></strong></p>
                    <?php if (!empty($venta['observaciones'])): ?>
                        <p><strong>Observaciones:</strong> <?= htmlspecialchars($venta['observaciones']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4">
                <h4>Productos</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($detalles)): ?>
                                <tr><td colspan="4" class="text-center">No hay productos en esta venta.</td></tr>
                            <?php else: ?>
                                <?php foreach ($detalles as $detalle): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
                                        <td><?= number_format($detalle['cantidad'], 2) ?></td>
                                        <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                                        <td>$<?= number_format($detalle['subtotal'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="3" class="text-end">Total:</th>
                                <th>$<?= number_format($venta['total'], 2) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="index.php?modulo=ventas&accion=index" class="btn btn-secondary">Volver</a>
            <?php if ($venta['estado'] == 'completada' && (esAdmin() || tieneRol('encargado'))): ?>
                <a href="index.php?modulo=ventas&accion=cancelar&id=<?= $venta['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Cancelar esta venta? Se restaurará el stock.')">
                    Cancelar Venta
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>