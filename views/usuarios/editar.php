<div class="container mt-4">
    <h2>Editar Usuario</h2>
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form action="index.php?modulo=usuarios&accion=actualizar" method="POST">
        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" name="apellido" id="apellido" class="form-control" value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email *</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Nueva Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Dejar vacío para mantener la actual">
            </div>
            <div class="col-md-6 mb-3">
                <label for="rol_id" class="form-label">Rol *</label>
                <select name="rol_id" id="rol_id" class="form-select" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['id'] ?>" <?= ($usuario['rol_id'] == $rol['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rol['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">URL de foto</label>
            <input type="text" name="foto" id="foto" class="form-control" value="<?= htmlspecialchars($usuario['foto'] ?? '') ?>">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="activo" id="activo" class="form-check-input" <?= $usuario['activo'] ? 'checked' : '' ?>>
            <label for="activo" class="form-check-label">Activo</label>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="index.php?modulo=usuarios&accion=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
