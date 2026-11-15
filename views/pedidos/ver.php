<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Pedido <?= htmlspecialchars($pedido['numero']) ?></h2>
            <span class="badge <?= [
                'pendiente' => 'bg-warning',
                'confirmado' => 'bg-info',
                'en_produccion' => 'bg-danger',
                'listo' => 'bg-success',
                'entregado' => 'bg-secondary',
                'cancelado' => 'bg-dark'
            ][$pedido['estado']] ?? 'bg-secondary' ?>">
                <?= ucfirst(str_replace('_', ' ', $pedido['estado'])) ?>
            </span>
        </div>
        <div class="card-body">
            <?php if ($mensaje = getMensaje()): ?>
                <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente_nombre'] ?? '') ?> <?= htmlspecialchars($pedido['cliente_apellido'] ?? '') ?></p>
                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['cliente_telefono'] ?? '-') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($pedido['cliente_email'] ?? '-') ?></p>
                    <p><strong>Usuario:</strong> <?= htmlspecialchars($pedido['usuario_nombre']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Fecha Pedido:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?></p>
                    <p><strong>Fecha Entrega:</strong> <?= date('d/m/Y', strtotime($pedido['fecha_entrega'])) ?>
                        <?php if ($pedido['hora_entrega']): ?>
                            a las <?= date('H:i', strtotime($pedido['hora_entrega'])) ?>
                        <?php endif; ?>
                    </p>
                    <p><strong>Subtotal:</strong> $<?= number_format($pedido['subtotal'], 2) ?></p>
                    <p><strong>Descuento:</strong> $<?= number_format($pedido['descuento'], 2) ?></p>
                    <p><strong>Total:</strong> <strong class="text-success">$<?= number_format($pedido['total'], 2) ?></strong></p>
                    <p><strong>Seña:</strong> $<?= number_format($pedido['senia'], 2) ?></p>
                    <p><strong>Saldo:</strong> <span class="<?= $pedido['saldo'] > 0 ? 'text-danger' : 'text-success' ?>">$<?= number_format($pedido['saldo'], 2) ?></span></p>
                </div>
            </div>

            <?php if (!empty($pedido['observaciones'])): ?>
                <div class="mt-3">
                    <h6>Observaciones:</h6>
                    <p><?= nl2br(htmlspecialchars($pedido['observaciones'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Productos -->
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
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($detalles)): ?>
                                <tr><td colspan="5" class="text-center">No hay productos en este pedido.</td></tr>
                            <?php else: ?>
                                <?php foreach ($detalles as $detalle): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
                                        <td><?= number_format($detalle['cantidad'], 2) ?></td>
                                        <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                                        <td>$<?= number_format($detalle['subtotal'], 2) ?></td>
                                        <td><?= htmlspecialchars($detalle['observaciones'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="3" class="text-end">Total:</th>
                                <th>$<?= number_format($pedido['total'], 2) ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Seguimiento -->
            <div class="mt-4">
                <h4>Seguimiento</h4>
                <div class="timeline">
                    <?php foreach ($seguimiento as $item): ?>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-<?= [
                                    'pendiente' => 'warning',
                                    'confirmado' => 'info',
                                    'en_produccion' => 'danger',
                                    'listo' => 'success',
                                    'entregado' => 'secondary',
                                    'cancelado' => 'dark'
                                ][$item['estado']] ?? 'secondary' ?>">
                                    <?= ucfirst(str_replace('_', ' ', $item['estado'])) ?>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-0">
                                    <strong><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></strong>
                                    - <?= htmlspecialchars($item['usuario_nombre']) ?>
                                </p>
                                <?php if ($item['observacion']): ?>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($item['observacion']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Acciones según estado -->
            <?php if (!in_array($pedido['estado'], ['entregado', 'cancelado'])): ?>
                <div class="mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Cambiar Estado</h5>
                            <form action="index.php?modulo=pedidos&accion=cambiarEstado" method="POST" class="row g-3">
                                <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
                                <div class="col-md-6">
                                    <select name="estado" class="form-select" required>
                                        <option value="">Seleccionar...</option>
                                        <?php if ($pedido['estado'] == 'pendiente'): ?>
                                            <option value="confirmado">Confirmar</option>
                                        <?php endif; ?>
                                        <?php if (in_array($pedido['estado'], ['confirmado', 'pendiente'])): ?>
                                            <option value="en_produccion">En Producción</option>
                                        <?php endif; ?>
                                        <?php if (in_array($pedido['estado'], ['en_produccion', 'confirmado'])): ?>
                                            <option value="listo">Listo</option>
                                        <?php endif; ?>
                                        <?php if (in_array($pedido['estado'], ['listo', 'en_produccion', 'confirmado'])): ?>
                                            <option value="entregado">Entregado</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="observacion" class="form-control" placeholder="Observación">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h5>Registrar Pago</h5>
                            <form action="index.php?modulo=pedidos&accion=registrarPago" method="POST" class="row g-3">
                                <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
                                <div class="col-md-6">
                                    <input type="number" step="0.01" name="monto" class="form-control" placeholder="Monto a pagar" required>
                                </div>
                                <div class="col-md-4">
                                    <span class="form-control bg-light">Saldo: $<?= number_format($pedido['saldo'], 2) ?></span>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success">Pagar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!in_array($pedido['estado'], ['entregado', 'cancelado']) && (esAdmin() || tieneRol('encargado'))): ?>
                <div class="mt-3">
                    <a href="index.php?modulo=pedidos&accion=cancelar&id=<?= $pedido['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Cancelar este pedido?')">
                        <i class="bi bi-x-circle"></i> Cancelar Pedido
                    </a>
                </div>
            <?php endif; ?>

        </div>
        <div class="card-footer">
            <a href="index.php?modulo=pedidos&accion=index" class="btn btn-secondary">Volver</a>
            <?php if ($pedido['estado'] == 'pendiente' && (esAdmin() || tieneRol('encargado'))): ?>
                <a href="index.php?modulo=pedidos&accion=editar&id=<?= $pedido['id'] ?>" class="btn btn-warning">Editar</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline .d-flex {
    position: relative;
}
.timeline .d-flex::before {
    content: '';
    position: absolute;
    left: -16px;
    top: 8px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #dee2e6;
}
</style>