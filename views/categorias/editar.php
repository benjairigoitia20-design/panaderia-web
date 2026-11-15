<div class="container mt-4">
    <h2>Editar Categoría</h2>
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form action="index.php?modulo=categorias&accion=actualizar" method="POST">
        <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="<?= htmlspecialchars($categoria['nombre']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3" class="form-control"><?= htmlspecialchars($categoria['descripcion']) ?></textarea>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="activo" id="activo" class="form-check-input" <?= $categoria['activo'] ? 'checked' : '' ?>>
            <label for="activo" class="form-check-label">Activo</label>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="index.php?modulo=categorias&accion=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>