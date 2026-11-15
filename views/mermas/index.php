<div class="container mt-4">
    <h2>Registro de Mermas</h2>
    <?php if (esAdmin() || tieneRol('panadero') || tieneRol('encargado')): ?>
        <a href="index.php?modulo=mermas&accion=crear" class="btn btn-success mb-3">
            <i class="bi bi-plus-circle"></i> Registrar Merma
        </a>
    <?php endif; ?>

    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Producto/Ingrediente</th>
                    <th>Cantidad</th>
                    <th>Costo Estimado</th>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mermas)): ?>
                    <tr><td colspan="8" class="text-center">No hay mermas registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($mermas as $merma): ?>
                        <tr>
                            <td><?= $merma['id'] ?></td>
                            <td>
                                <span class="badge bg-warning">
                                    <?= ucfirst(str_replace('_', ' ', $merma['tipo'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($merma['producto_nombre']): ?>
                                    <?= htmlspecialchars($merma['producto_nombre']) ?>
                                <?php elseif ($merma['ingrediente_nombre']): ?>
                                    <?= htmlspecialchars($merma['ingrediente_nombre']) ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($merma['cantidad'], 2) ?> <?= htmlspecialchars($merma['unidad_nombre'] ?? '') ?></td>
                            <td>$<?= number_format($merma['costo_estimado'], 2) ?></td>
                            <td><?= date('d/m/Y', strtotime($merma['fecha'])) ?></td>
                            <td><?= htmlspecialchars($merma['usuario_nombre'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($merma['motivo'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>