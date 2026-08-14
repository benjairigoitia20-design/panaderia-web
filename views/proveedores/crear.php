<div class="container mt-4">
    <h2>Nuevo Proveedor</h2>
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form action="index.php?modulo=proveedores&accion=guardar" method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="razon_social" class="form-label">Razón Social *</label>
                <input type="text" name="razon_social" id="razon_social" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="cuit" class="form-label">CUIT</label>
                <input type="text" name="cuit" id="cuit" class="form-control" placeholder="XX-XXXXXXXX-X">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <textarea name="direccion" id="direccion" rows="2" class="form-control"></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="contacto_nombre" class="form-label">Nombre de Contacto</label>
                <input type="text" name="contacto_nombre" id="contacto_nombre" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="contacto_telefono" class="form-label">Teléfono de Contacto</label>
                <input type="text" name="contacto_telefono" id="contacto_telefono" class="form-control">
            </div>
        </div>
        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" id="observaciones" rows="2" class="form-control"></textarea>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="estado" id="estado" class="form-check-input" checked>
            <label for="estado" class="form-check-label">Activo</label>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="index.php?modulo=proveedores&accion=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>