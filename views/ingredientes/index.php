<div class="container mt-4">
    <h2>Gestión de Ingredientes / Insumos</h2>
    <?php if (esAdmin() || tieneRol('panadero')): ?>
        <a href="index.php?modulo=ingredientes&accion=crear" class="btn btn-success mb-3">
            <i class="bi bi-plus-circle"></i> Nuevo Ingrediente
        </a>
    <?php endif; ?>

    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Unidad</th>
                    <th>Stock</th>
                    <th>Stock Mínimo</th>
                    <th>Costo Unitario</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ingredientes)): ?>
                    <tr><td colspan="10" class="text-center">No hay ingredientes registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($ingredientes as $ingrediente): ?>
                        <tr>
                            <td><?= $ingrediente['id'] ?></td>
                            <td><?= htmlspecialchars($ingrediente['codigo'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($ingrediente['nombre']) ?></td>
                            <td><?= htmlspecialchars($ingrediente['categoria'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($ingrediente['unidad_abreviatura'] ?? '') ?></td>
                            <td><?= number_format($ingrediente['stock_actual'], 2) ?></td>
                            <td><?= number_format($ingrediente['stock_minimo'], 2) ?></td>
                            <td>$<?= number_format($ingrediente['costo_unitario'], 4) ?></td>
                            <td>
                                <span class="badge <?= $ingrediente['estado'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $ingrediente['estado'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (esAdmin() || tieneRol('panadero')): ?>
                                    <a href="index.php?modulo=ingredientes&accion=editar&id=<?= $ingrediente['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="index.php?modulo=ingredientes&accion=eliminar&id=<?= $ingrediente['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este ingrediente?')">Eliminar</a>
                                <?php else: ?>
                                    <span class="text-muted">Solo lectura</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>