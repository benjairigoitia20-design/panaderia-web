<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Orden de Compra <?= htmlspecialchars($orden['numero']) ?></h2>
            <span class="badge <?= [
                'borrador' => 'bg-secondary',
                'pendiente' => 'bg-warning',
                'recibida' => 'bg-success',
                'parcial' => 'bg-info',
                'cancelada' => 'bg-danger'
            ][$orden['estado']] ?? 'bg-secondary' ?>">
                <?= ucfirst($orden['estado']) ?>
            </span>
        </div>
        <div class="card-body">
            <?php if ($mensaje = getMensaje()): ?>
                <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Proveedor:</strong> <?= htmlspecialchars($orden['proveedor_nombre'] ?? '-') ?></p>
                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($orden['proveedor_telefono'] ?? '-') ?></p>
                    <p><strong>Fecha de Orden:</strong> <?= date('d/m/Y', strtotime($orden['fecha_orden'])) ?></p>
                    <?php if ($orden['fecha_recepcion']): ?>
                        <p><strong>Fecha de Recepción:</strong> <?= date('d/m/Y', strtotime($orden['fecha_recepcion'])) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <p><strong>Usuario:</strong> <?= htmlspecialchars($orden['usuario_nombre'] ?? '-') ?></p>
                    <p><strong>Subtotal:</strong> $<?= number_format($orden['subtotal'], 2) ?></p>
                    <p><strong>Descuento:</strong> $<?= number_format($orden['descuento'], 2) ?></p>
                    <p><strong>Total:</strong> <strong class="text-success">$<?= number_format($orden['total'], 2) ?></strong></p>
                </div>
            </div>

            <?php if (!empty($orden['observaciones'])): ?>
                <div class="mt-3">
                    <h6>Observaciones:</h6>
                    <p><?= nl2br(htmlspecialchars($orden['observaciones'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Productos -->
            <div class="mt-4">
                <h4>Productos</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Ingrediente</th>
                                <th>Cantidad</th>
                                <th>Cant. Recibida</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal</th>
                                <?php if ($orden['estado'] == 'pendiente'): ?>
                                    <th>Recibir</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($detalles)): ?>
                                <tr><td colspan="6" class="text-center">No hay productos en esta orden.</td></tr>
                            <?php else: ?>
                                <?php if ($orden['estado'] == 'pendiente'): ?>
                                    <form action="index.php?modulo=compras&accion=recibir" method="POST">
                                        <input type="hidden" name="id" value="<?= $orden['id'] ?>">
                                        <?php foreach ($detalles as $detalle): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($detalle['ingrediente_nombre']) ?></td>
                                                <td><?= number_format($detalle['cantidad'], 2) ?> <?= $detalle['unidad_abreviatura'] ?></td>
                                                <td>
                                                    <input type="number" step="0.01" name="cantidad_<?= $detalle['id'] ?>" class="form-control form-control-sm" value="<?= $detalle['cantidad'] ?>" required>
                                                </td>
                                                <td>$<?= number_format($detalle['precio_unitario'], 4) ?></td>
                                                <td>$<?= number_format($detalle['subtotal'], 2) ?></td>
                                                <td>
                                                    <span class="text-muted">Se recibirá</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                <?php else: ?>
                                    <?php foreach ($detalles as $detalle): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($detalle['ingrediente_nombre']) ?></td>
                                            <td><?= number_format($detalle['cantidad'], 2) ?> <?= $detalle['unidad_abreviatura'] ?></td>
                                            <td><?= number_format($detalle['cantidad_recibida'], 2) ?> <?= $detalle['unidad_abreviatura'] ?></td>
                                            <td>$<?= number_format($detalle['precio_unitario'], 4) ?></td>
                                            <td>$<?= number_format($detalle['subtotal'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="<?= $orden['estado'] == 'pendiente' ? 4 : 3 ?>" class="text-end">Total:</th>
                                <th>$<?= number_format($orden['total'], 2) ?></th>
                                <?php if ($orden['estado'] == 'pendiente'): ?>
                                    <th></th>
                                <?php endif; ?>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Acciones -->
            <?php if ($orden['estado'] == 'pendiente' && (esAdmin() || tieneRol('encargado'))): ?>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success" onclick="return confirm('¿Confirmar recepción? Se actualizará el stock y los precios.')">
                        <i class="bi bi-check-circle"></i> Recibir Compra
                    </button>
                    </form>
                    <a href="index.php?modulo=compras&accion=cancelar&id=<?= $orden['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Cancelar esta orden?')">
                        <i class="bi bi-x-circle"></i> Cancelar Orden
                    </a>
                </div>
            <?php endif; ?>

        </div>
        <div class="card-footer">
            <a href="index.php?modulo=compras&accion=index" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>