<div class="container mt-4">
    <h2>Nueva Orden de Producción</h2>
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form action="index.php?modulo=produccion&accion=guardar" method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="producto_id" class="form-label">Producto *</label>
                <select name="producto_id" id="producto_id" class="form-select" required>
                    <option value="">Seleccionar producto...</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto['id'] ?>"><?= htmlspecialchars($producto['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="receta_id" class="form-label">Receta *</label>
                <select name="receta_id" id="receta_id" class="form-select" required>
                    <option value="">Primero selecciona un producto</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="cantidad_planificada" class="form-label">Cantidad a Producir *</label>
                <input type="number" step="0.01" name="cantidad_planificada" id="cantidad_planificada" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="fecha_produccion" class="form-label">Fecha de Producción *</label>
                <input type="date" name="fecha_produccion" id="fecha_produccion" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="responsable" class="form-label">Responsable</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['usuario_nombre']) ?>" disabled>
                <input type="hidden" name="responsable_id" value="<?= $_SESSION['usuario_id'] ?>">
            </div>
        </div>
        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" id="observaciones" rows="3" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Crear Orden</button>
        <a href="index.php?modulo=produccion&accion=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productoSelect = document.getElementById('producto_id');
    const recetaSelect = document.getElementById('receta_id');

    productoSelect.addEventListener('change', function() {
        const productoId = this.value;
        if (!productoId) {
            recetaSelect.innerHTML = '<option value="">Primero selecciona un producto</option>';
            return;
        }

        // Cargar recetas via AJAX
        fetch('index.php?modulo=produccion&accion=obtenerRecetas&producto_id=' + productoId)
            .then(response => response.json())
            .then(data => {
                recetaSelect.innerHTML = '<option value="">Seleccionar receta...</option>';
                data.forEach(receta => {
                    const option = document.createElement('option');
                    option.value = receta.id;
                    option.textContent = receta.nombre + ' (Rinde: ' + receta.rendimiento + ' ' + receta.unidad_rendimiento + ')';
                    recetaSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                recetaSelect.innerHTML = '<option value="">Error al cargar recetas</option>';
            });
    });
});
</script>