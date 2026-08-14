<div class="container mt-4">
    <h2>Editar Receta</h2>
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form action="index.php?modulo=recetas&accion=actualizar" method="POST" id="formReceta">
        <input type="hidden" name="id" value="<?= $receta['id'] ?>">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre de la Receta *</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="<?= htmlspecialchars($receta['nombre']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="producto_id" class="form-label">Producto *</label>
                <select name="producto_id" id="producto_id" class="form-select" required>
                    <option value="">Seleccionar producto...</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto['id'] ?>" <?= $producto['id'] == $receta['producto_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($producto['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="rendimiento" class="form-label">Rendimiento *</label>
                <input type="number" step="0.01" name="rendimiento" id="rendimiento" class="form-control" value="<?= $receta['rendimiento'] ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="unidad_rendimiento" class="form-label">Unidad de Rendimiento *</label>
                <input type="text" name="unidad_rendimiento" id="unidad_rendimiento" class="form-control" value="<?= htmlspecialchars($receta['unidad_rendimiento']) ?>" required>
            </div>
            <div class="col-md-2 mb-3">
                <label for="tiempo_preparacion" class="form-label">Prep. (min)</label>
                <input type="number" name="tiempo_preparacion" id="tiempo_preparacion" class="form-control" value="<?= $receta['tiempo_preparacion'] ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label for="tiempo_coccion" class="form-label">Cocción (min)</label>
                <input type="number" name="tiempo_coccion" id="tiempo_coccion" class="form-control" value="<?= $receta['tiempo_coccion'] ?>">
            </div>
        </div>
        <div class="mb-3">
            <label for="instrucciones" class="form-label">Instrucciones</label>
            <textarea name="instrucciones" id="instrucciones" rows="3" class="form-control"><?= htmlspecialchars($receta['instrucciones'] ?? '') ?></textarea>
        </div>

        <h5 class="mt-4">Ingredientes</h5>
        <div id="ingredientes-container">
            <?php if (empty($ingredientes_receta)): ?>
                <div class="row ingrediente-row">
                    <div class="col-md-4">
                        <select name="ingredientes[0][ingrediente_id]" class="form-select" required>
                            <option value="">Seleccionar ingrediente...</option>
                            <?php foreach ($ingredientes as $ing): ?>
                                <option value="<?= $ing['id'] ?>"><?= htmlspecialchars($ing['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="ingredientes[0][cantidad]" class="form-control" placeholder="Cantidad" required>
                    </div>
                    <div class="col-md-3">
                        <select name="ingredientes[0][unidad_medida_id]" class="form-select" required>
                            <option value="">Unidad</option>
                            <?php foreach ($unidades as $unidad): ?>
                                <option value="<?= $unidad['id'] ?>"><?= htmlspecialchars($unidad['abreviatura']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm eliminar-ingrediente">✕</button>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($ingredientes_receta as $index => $ing): ?>
                    <div class="row ingrediente-row">
                        <div class="col-md-4">
                            <select name="ingredientes[<?= $index ?>][ingrediente_id]" class="form-select" required>
                                <option value="">Seleccionar ingrediente...</option>
                                <?php foreach ($ingredientes as $ingrediente): ?>
                                    <option value="<?= $ingrediente['id'] ?>" <?= $ingrediente['id'] == $ing['ingrediente_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ingrediente['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" name="ingredientes[<?= $index ?>][cantidad]" class="form-control" placeholder="Cantidad" value="<?= $ing['cantidad'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <select name="ingredientes[<?= $index ?>][unidad_medida_id]" class="form-select" required>
                                <option value="">Unidad</option>
                                <?php foreach ($unidades as $unidad): ?>
                                    <option value="<?= $unidad['id'] ?>" <?= $unidad['id'] == $ing['unidad_medida_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($unidad['abreviatura']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm eliminar-ingrediente">✕</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-secondary btn-sm mt-2" id="agregar-ingrediente">+ Agregar Ingrediente</button>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Actualizar Receta</button>
            <a href="index.php?modulo=recetas&accion=index" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let index = <?= count($ingredientes_receta) ?: 1 ?>;
    
    document.getElementById('agregar-ingrediente').addEventListener('click', function() {
        const container = document.getElementById('ingredientes-container');
        const row = document.querySelector('.ingrediente-row').cloneNode(true);
        
        row.querySelectorAll('select, input').forEach(function(el) {
            const name = el.getAttribute('name');
            if (name) {
                el.setAttribute('name', name.replace(/\[[0-9]+\]/, '[' + index + ']'));
            }
            if (el.tagName === 'INPUT') {
                el.value = '';
            }
            if (el.tagName === 'SELECT') {
                el.selectedIndex = 0;
            }
        });
        
        container.appendChild(row);
        index++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('eliminar-ingrediente')) {
            const rows = document.querySelectorAll('.ingrediente-row');
            if (rows.length > 1) {
                e.target.closest('.ingrediente-row').remove();
            } else {
                alert('Debe haber al menos un ingrediente.');
            }
        }
    });
});
</script>