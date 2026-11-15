<div class="container mt-4">
    <h2>Nuevo Ingrediente</h2>
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form action="index.php?modulo=ingredientes&accion=guardar" method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="codigo" class="form-label">Código</label>
                <input type="text" name="codigo" id="codigo" class="form-control" placeholder="Ej: HAR-001">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="categoria" class="form-label">Categoría</label>
                <input type="text" name="categoria" id="categoria" class="form-control" placeholder="Ej: Harinas">
            </div>
            <div class="col-md-4 mb-3">
                <label for="unidad_medida_id" class="form-label">Unidad de Medida *</label>
                <select name="unidad_medida_id" id="unidad_medida_id" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($unidades as $unidad): ?>
                        <option value="<?= $unidad['id'] ?>"><?= htmlspecialchars($unidad['nombre']) ?> (<?= $unidad['abreviatura'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="proveedor_principal" class="form-label">Proveedor Principal</label>
                <input type="text" name="proveedor_principal" id="proveedor_principal" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="stock_actual" class="form-label">Stock Actual</label>
                <input type="number" step="0.01" name="stock_actual" id="stock_actual" class="form-control" value="0">
            </div>
            <div class="col-md-4 mb-3">
                <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                <input type="number" step="0.01" name="stock_minimo" id="stock_minimo" class="form-control" value="0">
            </div>
            <div class="col-md-4 mb-3">
                <label for="costo_unitario" class="form-label">Costo Unitario</label>
                <input type="number" step="0.0001" name="costo_unitario" id="costo_unitario" class="form-control" value="0">
            </div>
        </div>
        <div class="mb-3">
            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="estado" id="estado" class="form-check-input" checked>
            <label for="estado" class="form-check-label">Activo</label>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="index.php?modulo=ingredientes&accion=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>