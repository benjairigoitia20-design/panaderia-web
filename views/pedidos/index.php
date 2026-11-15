<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2>Gestión de Pedidos</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="index.php?modulo=pedidos&accion=crear" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Nuevo Pedido
            </a>
            <a href="index.php?modulo=pedidos&accion=calendario" class="btn btn-info">
                <i class="bi bi-calendar"></i> Calendario
            </a>
        </div>
    </div>

    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <!-- Resumen rápido -->
    <div class="row mt-3">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5>Total</h5>
                    <h3><?= $estadisticas['total'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h5>Pendientes</h5>
                    <h3><?= $estadisticas['pendientes'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5>Confirmados</h5>
                    <h3><?= $estadisticas['confirmados'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h5>En Producción</h5>
                    <h3><?= $estadisticas['en_produccion'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5>Listos</h5>
                    <h3><?= $estadisticas['listos'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h5>Entregados</h5>
                    <h3><?= $estadisticas['entregados'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mt-4">
        <div class="btn-group" role="group">
            <a href="index.php?modulo=pedidos&accion=index&estado=todos" class="btn btn-outline-secondary <?= ($_GET['estado'] ?? 'todos') == 'todos' ? 'active' : '' ?>">Todos</a>
            <a href="index.php?modulo=pedidos&accion=index&estado=pendiente" class="btn btn-outline-warning <?= ($_GET['estado'] ?? '') == 'pendiente' ? 'active' : '' ?>">Pendientes</a>
            <a href="index.php?modulo=pedidos&accion=index&estado=confirmado" class="btn btn-outline-info <?= ($_GET['estado'] ?? '') == 'confirmado' ? 'active' : '' ?>">Confirmados</a>
            <a href="index.php?modulo=pedidos&accion=index&estado=en_produccion" class="btn btn-outline-danger <?= ($_GET['estado'] ?? '') == 'en_produccion' ? 'active' : '' ?>">En Producción</a>
            <a href="index.php?modulo=pedidos&accion=index&estado=listo" class="btn btn-outline-success <?= ($_GET['estado'] ?? '') == 'listo' ? 'active' : '' ?>">Listos</a>
            <a href="index.php?modulo=pedidos&accion=index&estado=entregado" class="btn btn-outline-secondary <?= ($_GET['estado'] ?? '') == 'entregado' ? 'active' : '' ?>">Entregados</a>
        </div>
    </div>

    <!-- Listado -->
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Fecha Pedido</th>
                    <th>Fecha Entrega</th>
                    <th>Total</th>
                    <th>Seña</th>
                    <th>Saldo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pedidos)): ?>
                    <tr><td colspan="9" class="text-center">No hay pedidos registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($pedido['numero']) ?></strong></td>
                            <td><?= htmlspecialchars($pedido['cliente_nombre'] ?? '') ?> <?= htmlspecialchars($pedido['cliente_apellido'] ?? '') ?></td>
                            <td><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($pedido['fecha_entrega'])) ?>
                                <?php if ($pedido['hora_entrega']): ?>
                                    <br><small><?= date('H:i', strtotime($pedido['hora_entrega'])) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>$<?= number_format($pedido['total'], 2) ?></td>
                            <td>$<?= number_format($pedido['senia'], 2) ?></td>
                            <td class="<?= $pedido['saldo'] > 0 ? 'text-danger' : 'text-success' ?>">
                                $<?= number_format($pedido['saldo'], 2) ?>
                            </td>
                            <td>
                                <?php
                                $badgeClass = [
                                    'pendiente' => 'bg-warning',
                                    'confirmado' => 'bg-info',
                                    'en_produccion' => 'bg-danger',
                                    'listo' => 'bg-success',
                                    'entregado' => 'bg-secondary',
                                    'cancelado' => 'bg-dark'
                                ][$pedido['estado']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst(str_replace('_', ' ', $pedido['estado'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="index.php?modulo=pedidos&accion=ver&id=<?= $pedido['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                                <?php if (!in_array($pedido['estado'], ['entregado', 'cancelado']) && (esAdmin() || tieneRol('encargado'))): ?>
                                    <a href="index.php?modulo=pedidos&accion=cancelar&id=<?= $pedido['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Cancelar este pedido?')">Cancelar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
   