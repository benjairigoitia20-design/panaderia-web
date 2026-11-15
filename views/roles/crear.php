<div class="container mt-4">
    <h2>Nuevo Rol</h2>
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form action="index.php?modulo=roles&accion=guardar" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del rol *</label>
            <input type="text" name="nombre" id="nombre" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="index.php?modulo=roles&accion=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
