<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Orden de Producción <?= htmlspecialchars($orden['numero']) ?></h2>
            <span class="badge <?= [
                'planificada' => 'bg-secondary',
                'en_preparacion' => 'bg-info',
                'en_produccion' => 'bg-warning',
                'terminada' => 'bg-success',
                'cancelada' => 'bg-danger'
            ][$orden['estado']] ?? 'bg-secondary' ?>">
                <?= ucfirst(str_replace('_', ' ', $orden['estado'])) ?>
            </span>
        </div>
        <div class="card-body">
            <?php if ($mensaje = getMensaje()): ?>
                <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Producto:</strong> <?= htmlspecialchars($orden['producto_nombre']) ?></p>
                    <p><strong>Receta:</strong> <?= htmlspecialchars($orden['receta_nombre']) ?></p>
                    <p><strong>Cantidad Planificada:</strong> <?= number_format($orden['cantidad_planificada'], 2) ?></p>
                    <p><strong>Cantidad Producida:</strong> <?= number_format($orden['cantidad_producida'], 2) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($orden['fecha_produccion'])) ?></p>
                    <p><strong>Responsable:</strong> <?= htmlspecialchars($orden['responsable_nombre']) ?></p>
                    <p><strong>Fecha Inicio:</strong> <?= $orden['fecha_inicio'] ? date('d/m/Y H:i', strtotime($orden['fecha_inicio'])) : '-' ?></p>
                    <p><strong>Fecha Fin:</strong> <?= $orden['fecha_fin'] ? date('d/m/Y H:i', strtotime($orden['fecha_fin'])) : '-' ?></p>
                </div>
            </div>

            <?php if (!empty($orden['observaciones'])): ?>
                <div class="mt-3">
                    <h6>Observaciones:</h6>
                    <p><?= nl2br(htmlspecialchars($orden['observaciones'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Alertas de stock -->
            <?php if (!empty($faltantes) && $orden['estado'] == 'planificada'): ?>
                <div class="alert alert-danger mt-3">
                    <h6>⚠️ Faltan ingredientes para esta producción:</h6>
                    <ul>
                        <?php foreach ($faltantes as $falta): ?>
                            <li><?= htmlspecialchars($falta['ingrediente']) ?>: 
                                Necesario <?= number_format($falta['necesario'], 2) ?> <?= $falta['unidad'] ?>, 
                                Disponible <?= number_format($falta['disponible'], 2) ?> <?= $falta['unidad'] ?>, 
                                <strong>Faltante <?= number_format($falta['faltante'], 2) ?> <?= $falta['unidad'] ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Tabla de ingredientes -->
            <div class="mt-4">
                <h4>Ingredientes Necesarios</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Ingrediente</th>
                                <th>Cantidad Teórica</th>
                                <th>Cantidad Real</th>
                                <th>Merma</th>
                                <th>Unidad</th>
                                <th>Stock Actual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ingredientes)): ?>
                                <tr><td colspan="6" class="text-center">No hay ingredientes registrados.</td></tr>
                            <?php else: ?>
                                <?php foreach ($ingredientes as $ing): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($ing['ingrediente_nombre']) ?></td>
                                        <td><?= number_format($ing['cantidad_teorica'], 2) ?></td>
                                        <td><?= number_format($ing['cantidad_real'], 2) ?></td>
                                        <td><?= number_format($ing['cantidad_merma'], 2) ?></td>
                                        <td><?= htmlspecialchars($ing['unidad_abreviatura']) ?></td>
                                        <td class="<?= $ing['stock_actual'] < $ing['cantidad_teorica'] ? 'text-danger' : '' ?>">
                                            <?= number_format($ing['stock_actual'], 2) ?>
                                            <?php if ($ing['stock_actual'] < $ing['cantidad_teorica']): ?>
                                                <span class="badge bg-danger">Stock bajo</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Acciones según estado -->
            <?php if ($orden['estado'] == 'planificada' && (esAdmin() || tieneRol('panadero') || tieneRol('encargado'))): ?>
                <div class="mt-3">
                    <?php if (empty($faltantes)): ?>
                        <a href="index.php?modulo=produccion&accion=iniciar&id=<?= $orden['id'] ?>" class="btn btn-primary" onclick="return confirm('¿Iniciar producción? Se descontarán los ingredientes del stock.')">
                            ▶️ Iniciar Producción
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Faltan ingredientes para iniciar</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($orden['estado'] == 'en_produccion' && (esAdmin() || tieneRol('panadero') || tieneRol('encargado'))): ?>
                <div class="mt-3">
                    <form action="index.php?modulo=produccion&accion=finalizar" method="POST" class="row g-3">
                        <input type="hidden" name="id" value="<?= $orden['id'] ?>">
                        <div class="col-md-4">
                            <label for="cantidad_producida" class="form-label">Cantidad Producida *</label>
                            <input type="number" step="0.01" name="cantidad_producida" id="cantidad_producida" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="observaciones_fin" class="form-label">Observaciones</label>
                            <input type="text" name="observaciones" id="observaciones_fin" class="form-control" placeholder="Observaciones de la producción">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success" onclick="return confirm('¿Finalizar producción? Se descontarán los ingredientes y se agregará el producto al stock.')">
                                ✅ Finalizar
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (in_array($orden['estado'], ['planificada', 'en_preparacion']) && (esAdmin() || tieneRol('encargado'))): ?>
                <div class="mt-3">
                    <a href="index.php?modulo=produccion&accion=cancelar&id=<?= $orden['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Cancelar esta orden?')">
                        ❌ Cancelar Orden
                    </a>
                </div>
            <?php endif; ?>

        </div>
        <div class="card-footer">
            <a href="index.php?modulo=produccion&accion=index" class="btn btn-secondary">Volver</a>
        </div>
    </div>
</div>