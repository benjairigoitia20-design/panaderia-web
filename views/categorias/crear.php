<div class="container mt-4">
    <h2>Nueva Categoría</h2>
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form action="index.php?modulo=categorias&accion=guardar" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre *</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3" class="form-control"></textarea>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="activo" id="activo" class="form-check-input" checked>
            <label for="activo" class="form-check-label">Activo</label>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="index.php?modulo=categorias&accion=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>