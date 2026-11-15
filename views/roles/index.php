<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Roles y Permisos</h2>
        <a href="index.php?modulo=roles&accion=crear" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Nuevo Rol
        </a>
    </div>

    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form action="index.php?modulo=roles&accion=guardarPermisos" method="POST">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Rol</th>
                        <th>Usuarios</th>
                        <th>Permisos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $rol): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($rol['nombre']) ?></strong>
                            </td>
                            <td><?= $rol['usuarios_count'] ?></td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($permisos as $permiso): ?>
                                        <?php $checked = in_array($permiso['id'], $permisosPorRol[$rol['id']] ?? []); ?>
                                        <label class="form-check-label badge rounded-pill <?= $checked ? 'bg-success' : 'bg-light text-dark border' ?>" style="padding: 0.5rem 0.8rem; cursor: pointer;">
                                            <input type="checkbox" name="permisos[<?= $rol['id'] ?>][]" value="<?= $permiso['id'] ?>" <?= $checked ? 'checked' : '' ?> class="form-check-input me-1">
                                            <?= htmlspecialchars($permiso['descripcion']) ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td>
                                <a href="index.php?modulo=roles&accion=editar&id=<?= $rol['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="index.php?modulo=roles&accion=eliminar&id=<?= $rol['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Desea eliminar este rol?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3 text-end">
            <button type="submit" class="btn btn-primary">Guardar permisos</button>
        </div>
    </form>
</div>
