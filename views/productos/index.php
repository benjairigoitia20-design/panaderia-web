<div class="container mt-4">
    <h2>Gestión de Productos</h2>
    <?php if (esAdmin() || tieneRol('panadero')): ?>
        <a href="index.php?modulo=productos&accion=crear" class="btn btn-success mb-3">
            <i class="bi bi-plus-circle"></i> Nuevo Producto
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
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Categoría</th>
                    <th>Destacado</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                    <tr><td colspan="9" class="text-center">No hay productos registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?= $producto['id'] ?></td>
                            <td>
                                <?php if (!empty($producto['imagen'])): ?>
                                    <img src="assets/img/<?= $producto['imagen'] ?>" alt="Producto" width="50">
                                <?php else: ?>
                                    <span class="text-muted">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td>$<?= number_format($producto['precio'], 2) ?></td>
                            <td><?= $producto['stock'] ?></td>
                            <td><?= htmlspecialchars($producto['categoria_nombre'] ?? 'Sin categoría') ?></td>
                            <td><?= $producto['destacado'] ? '⭐ Sí' : 'No' ?></td>
                            <td>
                                <span class="badge <?= $producto['estado'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $producto['estado'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (esAdmin() || tieneRol('panadero')): ?>
                                    <a href="index.php?modulo=productos&accion=editar&id=<?= $producto['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="index.php?modulo=productos&accion=eliminar&id=<?= $producto['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
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