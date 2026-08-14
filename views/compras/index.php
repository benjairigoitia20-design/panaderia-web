<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2>Órdenes de Compra</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="index.php?modulo=compras&accion=crear" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Nueva Orden
            </a>
        </div>
    </div>

    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <!-- Resumen -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5>Total</h5>
                    <h3><?= $estadisticas['total'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5>Pendientes</h5>
                    <h3><?= $estadisticas['pendientes'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5>Recibidas</h5>
                    <h3><?= $estadisticas['recibidas'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h5>Canceladas</h5>
                    <h3><?= $estadisticas['canceladas'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mt-4">
        <div class="btn-group" role="group">
            <a href="index.php?modulo=compras&accion=index&estado=todos" class="btn btn-outline-secondary <?= ($_GET['estado'] ?? 'todos') == 'todos' ? 'active' : '' ?>">Todos</a>
            <a href="index.php?modulo=compras&accion=index&estado=pendiente" class="btn btn-outline-warning <?= ($_GET['estado'] ?? '') == 'pendiente' ? 'active' : '' ?>">Pendientes</a>
            <a href="index.php?modulo=compras&accion=index&estado=recibida" class="btn btn-outline-success <?= ($_GET['estado'] ?? '') == 'recibida' ? 'active' : '' ?>">Recibidas</a>
            <a href="index.php?modulo=compras&accion=index&estado=cancelada" class="btn btn-outline-danger <?= ($_GET['estado'] ?? '') == 'cancelada' ? 'active' : '' ?>">Canceladas</a>
        </div>
    </div>

    <!-- Listado -->
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Número</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ordenes)): ?>
                    <tr><td colspan="7" class="text-center">No hay órdenes de compra.</td></tr>
                <?php else: ?>
                    <?php foreach ($ordenes as $orden): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($orden['numero']) ?></strong></td>
                            <td><?= htmlspecialchars($orden['proveedor_nombre'] ?? '-') ?></td>
                            <td><?= date('d/m/Y', strtotime($orden['fecha_orden'])) ?></td>
                            <td>$<?= number_format($orden['total'], 2) ?></td>
                            <td>
                                <?php
                                $badgeClass = [
                                    'borrador' => 'bg-secondary',
                                    'pendiente' => 'bg-warning',
                                    'recibida' => 'bg-success',
                                    'parcial' => 'bg-info',
                                    'cancelada' => 'bg-danger'
                                ][$orden['estado']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($orden['estado']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($orden['usuario_nombre'] ?? '-') ?></td>
                            <td>
                                <a href="index.php?modulo=compras&accion=ver&id=<?= $orden['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                                <?php if ($orden['estado'] == 'pendiente' && (esAdmin() || tieneRol('encargado'))): ?>
                                    <a href="index.php?modulo=compras&accion=cancelar&id=<?= $orden['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Cancelar esta orden?')">Cancelar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>