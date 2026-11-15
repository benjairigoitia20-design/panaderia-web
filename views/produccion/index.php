<div class="container mt-4">
    <h2>Órdenes de Producción</h2>
    <?php if (esAdmin() || tieneRol('panadero') || tieneRol('encargado')): ?>
        <a href="index.php?modulo=produccion&accion=crear" class="btn btn-success mb-3">
            <i class="bi bi-plus-circle"></i> Nueva Orden
        </a>
    <?php endif; ?>

    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Número</th>
                    <th>Producto</th>
                    <th>Receta</th>
                    <th>Cant. Planif.</th>
                    <th>Cant. Prod.</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Responsable</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ordenes)): ?>
                    <tr><td colspan="9" class="text-center">No hay órdenes de producción.</td></tr>
                <?php else: ?>
                    <?php foreach ($ordenes as $orden): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($orden['numero']) ?></strong></td>
                            <td><?= htmlspecialchars($orden['producto_nombre'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($orden['receta_nombre'] ?? '-') ?></td>
                            <td><?= number_format($orden['cantidad_planificada'], 2) ?></td>
                            <td><?= number_format($orden['cantidad_producida'], 2) ?></td>
                            <td><?= date('d/m/Y', strtotime($orden['fecha_produccion'])) ?></td>
                            <td>
                                <?php
                                $badgeClass = [
                                    'planificada' => 'bg-secondary',
                                    'en_preparacion' => 'bg-info',
                                    'en_produccion' => 'bg-warning',
                                    'terminada' => 'bg-success',
                                    'cancelada' => 'bg-danger'
                                ][$orden['estado']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst(str_replace('_', ' ', $orden['estado'])) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($orden['responsable_nombre'] ?? '-') ?></td>
                            <td>
                                <a href="index.php?modulo=produccion&accion=ver&id=<?= $orden['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                                
                                <?php if ($orden['estado'] == 'planificada' && (esAdmin() || tieneRol('panadero') || tieneRol('encargado'))): ?>
                                    <a href="index.php?modulo=produccion&accion=iniciar&id=<?= $orden['id'] ?>" class="btn btn-sm btn-primary" onclick="return confirm('¿Iniciar producción? Se verificará el stock.')">Iniciar</a>
                                <?php endif; ?>
                                
                                <?php if ($orden['estado'] == 'en_produccion' && (esAdmin() || tieneRol('panadero') || tieneRol('encargado'))): ?>
                                    <a href="index.php?modulo=produccion&accion=ver&id=<?= $orden['id'] ?>" class="btn btn-sm btn-warning">Finalizar</a>
                                <?php endif; ?>
                                
                                <?php if (in_array($orden['estado'], ['planificada', 'en_preparacion']) && (esAdmin() || tieneRol('encargado'))): ?>
                                    <a href="index.php?modulo=produccion&accion=cancelar&id=<?= $orden['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Cancelar esta orden?')">Cancelar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>