<div class="container mt-4">
    <h2>Gestión de Proveedores</h2>
    <?php if (esAdmin() || tieneRol('encargado')): ?>
        <a href="index.php?modulo=proveedores&accion=crear" class="btn btn-success mb-3">
            <i class="bi bi-plus-circle"></i> Nuevo Proveedor
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
                    <th>Razón Social</th>
                    <th>CUIT</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Contacto</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($proveedores)): ?>
                    <tr><td colspan="8" class="text-center">No hay proveedores registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <tr>
                            <td><?= $proveedor['id'] ?></td>
                            <td><?= htmlspecialchars($proveedor['razon_social']) ?></td>
                            <td><?= htmlspecialchars($proveedor['cuit'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($proveedor['telefono'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($proveedor['email'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($proveedor['contacto_nombre'] ?? '-') ?></td>
                            <td>
                                <span class="badge <?= $proveedor['estado'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $proveedor['estado'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (esAdmin() || tieneRol('encargado')): ?>
                                    <a href="index.php?modulo=proveedores&accion=editar&id=<?= $proveedor['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <a href="index.php?modulo=proveedores&accion=eliminar&id=<?= $proveedor['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este proveedor?')">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>