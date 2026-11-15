<div class="container mt-4">
    <h2>Registrar Merma</h2>
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form action="index.php?modulo=mermas&accion=guardar" method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="tipo" class="form-label">Tipo de Merma *</label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <option value="produccion">Producción</option>
                    <option value="vencimiento">Vencimiento</option>
                    <option value="rotura">Rotura</option>
                    <option value="exceso">Exceso de producción</option>
                    <option value="no_vendido">No vendido</option>
                    <option value="error">Error de producción</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="tipo_item" class="form-label">Tipo de Item *</label>
                <select name="tipo_item" id="tipo_item" class="form-select" required>
                    <option value="ingrediente">Ingrediente</option>
                    <option value="producto">Producto</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3" id="producto_div">
                <label for="producto_id" class="form-label">Producto *</label>
                <select name="producto_id" id="producto_id" class="form-select">
                    <option value="">Seleccionar producto...</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto['id'] ?>"><?= htmlspecialchars($producto['nombre']) ?> (Stock: <?= $producto['stock'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3" id="ingrediente_div" style="display:none;">
                <label for="ingrediente_id" class="form-label">Ingrediente *</label>
                <select name="ingrediente_id" id="ingrediente_id" class="form-select">
                    <option value="">Seleccionar ingrediente...</option>
                    <?php foreach ($ingredientes as $ingrediente): ?>
                        <option value="<?= $ingrediente['id'] ?>"><?= htmlspecialchars($ingrediente['nombre']) ?> (Stock: <?= number_format($ingrediente['stock_actual'], 2) ?> <?= $ingrediente['unidad_abreviatura'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="cantidad" class="form-label">Cantidad *</label>
                <input type="number" step="0.01" name="cantidad" id="cantidad" class="form-control" required>
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
                <label for="fecha" class="form-label">Fecha *</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="motivo" class="form-label">Motivo</label>
                <input type="text" name="motivo" id="motivo" class="form-control" placeholder="Ej: Producto quemado">
            </div>
            <div class="col-md-6 mb-3">
                <label for="observacion" class="form-label">Observación</label>
                <input type="text" name="observacion" id="observacion" class="form-control" placeholder="Detalles adicionales">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Registrar Merma</button>
        <a href="index.php?modulo=mermas&accion=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoItem = document.getElementById('tipo_item');
    const productoDiv = document.getElementById('producto_div');
    const ingredienteDiv = document.getElementById('ingrediente_div');
    const productoSelect = document.getElementById('producto_id');
    const ingredienteSelect = document.getElementById('ingrediente_id');

    tipoItem.addEventListener('change', function() {
        if (this.value === 'producto') {
            productoDiv.style.display = 'block';
            ingredienteDiv.style.display = 'none';
            productoSelect.required = true;
            ingredienteSelect.required = false;
            productoSelect.value = '';
        } else {
            productoDiv.style.display = 'none';
            ingredienteDiv.style.display = 'block';
            productoSelect.required = false;
            ingredienteSelect.required = true;
            ingredienteSelect.value = '';
        }
    });
});
</script>