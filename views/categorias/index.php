<div class="container mt-4">
    <h2>Gestión de Categorías</h2>
    <?php if (esAdmin() || tieneRol('panadero')): ?>
        <a href="index.php?modulo=categorias&accion=crear" class="btn btn-success mb-3">
            <i class="bi bi-plus-circle"></i> Nueva Categoría
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
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categorias)): ?>
                    <tr><td colspan="5" class="text-center">No hay categorías registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?= $categoria['id'] ?></td>
                            <td><?= htmlspecialchars($categoria['nombre']) ?></td>
                            <td><?= htmlspecialchars($categoria['descripcion']) ?></td>
                            <td>
                                <span class="badge <?= $categoria['activo'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $categoria['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (esAdmin() || tieneRol('panadero')): ?>
                                    <a href="index.php?modulo=categorias&accion=editar&id=<?= $categoria['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="index.php?modulo=categorias&accion=eliminar&id=<?= $categoria['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta categoría?')">Eliminar</a>
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