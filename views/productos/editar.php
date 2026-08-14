<div class="container mt-4">
    <h2>Editar Producto</h2>
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <form action="index.php?modulo=productos&accion=actualizar" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $producto['id'] ?>">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="precio" class="form-label">Precio *</label>
                <input type="number" step="0.01" name="precio" id="precio" class="form-control" value="<?= $producto['precio'] ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" name="stock" id="stock" class="form-control" value="<?= $producto['stock'] ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="categoria_id" class="form-label">Categoría</label>
                <select name="categoria_id" id="categoria_id" class="form-select">
                    <option value="">Sin categoría</option>
                    <?php 
                    require_once __DIR__ . '/../../models/Categoria.php';
                    $categoriaModel = new Categoria();
                    $categorias = $categoriaModel->obtenerTodos(true);
                    foreach ($categorias as $cat): 
                    ?>
                        <option value="<?= $cat['id'] ?>" <?= isset($producto) && $producto['categoria_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3" class="form-control"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="imagen" class="form-label">Imagen</label>
                <input type="file" name="imagen" id="imagen" class="form-control" accept="image/*">
                <?php if (!empty($producto['imagen'])): ?>
                    <div class="mt-2">
                        <img src="assets/img/<?= $producto['imagen'] ?>" alt="Producto" width="80">
                        <small class="text-muted">(Dejar vacío para mantener la actual)</small>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="destacado" id="destacado" value="1" <?= $producto['destacado'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="destacado">Destacado</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="estado" id="estado" value="1" <?= $producto['estado'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="estado">Activo</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="index.php?modulo=productos&accion=index" class="btn btn-secondary">Cancelar</a>
    </form>
</div>