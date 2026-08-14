<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2>Historial de Ventas</h2>
        </div>
        <div class="col-md-4 text-end">
            <div class="alert alert-success">
                <strong>Total del día:</strong> $<?= number_format($total_dia, 2) ?>
            </div>
            <a href="index.php?modulo=ventas&accion=nueva" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Nueva Venta
            </a>
        </div>
    </div>

    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Número</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Usuario</th>
                    <th>Subtotal</th>
                    <th>Total</th>
                    <th>Pago</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventas)): ?>
                    <tr><td colspan="9" class="text-center">No hay ventas registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($venta['numero']) ?></strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                            <td><?= htmlspecialchars($venta['cliente_nombre'] ?? '') ?> <?= htmlspecialchars($venta['cliente_apellido'] ?? '') ?></td>
                            <td><?= htmlspecialchars($venta['usuario_nombre']) ?></td>
                            <td>$<?= number_format($venta['subtotal'], 2) ?></td>
                            <td><strong>$<?= number_format($venta['total'], 2) ?></strong></td>
                            <td><?= ucfirst($venta['medio_pago']) ?></td>
                            <td>
                                <span class="badge <?= $venta['estado'] == 'completada' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ucfirst($venta['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?modulo=ventas&accion=ver&id=<?= $venta['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                                <?php if ($venta['estado'] == 'completada' && (esAdmin() || tieneRol('encargado'))): ?>
                                    <a href="index.php?modulo=ventas&accion=cancelar&id=<?= $venta['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Cancelar esta venta? Se restaurará el stock.')">Cancelar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>