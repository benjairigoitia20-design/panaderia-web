<div class="container mt-4">
    <h2>Gestión de Recetas</h2>
    <?php if (esAdmin() || tieneRol('panadero')): ?>
        <a href="index.php?modulo=recetas&accion=crear" class="btn btn-success mb-3">
            <i class="bi bi-plus-circle"></i> Nueva Receta
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
                    <th>Nombre</th>
                    <th>Producto</th>
                    <th>Rendimiento</th>
                    <th>Costo Total</th>
                    <th>Costo por Unidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recetas)): ?>
                    <tr><td colspan="8" class="text-center">No hay recetas registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($recetas as $receta): ?>
                        <tr>
                            <td><?= $receta['id'] ?></td>
                            <td><?= htmlspecialchars($receta['nombre']) ?></td>
                            <td><?= htmlspecialchars($receta['producto_nombre'] ?? '-') ?></td>
                            <td><?= $receta['rendimiento'] ?> <?= $receta['unidad_rendimiento'] ?></td>
                            <td>$<?= number_format($receta['costo_total'], 2) ?></td>
                            <td>$<?= number_format($receta['costo_por_unidad'], 2) ?></td>
                            <td>
                                <span class="badge <?= $receta['estado'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $receta['estado'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (esAdmin() || tieneRol('panadero')): ?>
                                    <a href="index.php?modulo=recetas&accion=ver&id=<?= $receta['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                                    <a href="index.php?modulo=recetas&accion=editar&id=<?= $receta['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="index.php?modulo=recetas&accion=eliminar&id=<?= $receta['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta receta?')">Eliminar</a>
                                <?php else: ?>
                                    <a href="index.php?modulo=recetas&accion=ver&id=<?= $receta['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>