<div class="container mt-4">
    <h2>Gestión de Caja</h2>
    
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <!-- Estado actual de la caja -->
    <div class="card mb-4">
        <div class="card-header bg-<?= $caja_actual ? 'success' : 'secondary' ?> text-white">
            <h5 class="card-title mb-0">
                <?= $caja_actual ? '🟢 Caja Abierta' : '🔴 Caja Cerrada' ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($caja_actual): ?>
                <div class="row">
                    <div class="col-md-3">
                        <p><strong>Monto Inicial:</strong> $<?= number_format($caja_actual['monto_inicial'], 2) ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Usuario Apertura:</strong> <?= htmlspecialchars($caja_actual['usuario_apertura_id']) ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>Fecha Apertura:</strong> <?= date('d/m/Y H:i', strtotime($caja_actual['fecha_apertura'])) ?></p>
                    </div>
                    <div class="col-md-3">
                        <?php if (esAdmin() || tieneRol('encargado')): ?>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalCerrarCaja">Cerrar Caja</button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Movimientos de caja -->
                <div class="mt-3">
                    <h6>Últimos movimientos</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                    <th>Descripción</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($movimientos)): ?>
                                    <tr><td colspan="5" class="text-center">No hay movimientos.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($movimientos as $mov): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($mov['created_at'])) ?></td>
                                            <td>
                                                <span class="badge <?= in_array($mov['tipo'], ['venta', 'ingreso']) ? 'bg-success' : 'bg-danger' ?>">
                                                    <?= ucfirst($mov['tipo']) ?>
                                                </span>
                                            </td>
                                            <td>$<?= number_format($mov['monto'], 2) ?></td>
                                            <td><?= htmlspecialchars($mov['descripcion']) ?></td>
                                            <td><?= htmlspecialchars($mov['usuario_nombre']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <?php if (esAdmin() || tieneRol('encargado')): ?>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAbrirCaja">Abrir Caja</button>
                <?php else: ?>
                    <p class="text-muted">La caja está cerrada. Solicita al encargado que la abra.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Historial de cierres -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Historial de Cierres</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha Apertura</th>
                            <th>Fecha Cierre</th>
                            <th>Usuario Apertura</th>
                            <th>Usuario Cierre</th>
                            <th>Monto Inicial</th>
                            <th>Monto Esperado</th>
                            <th>Monto Contado</th>
                            <th>Diferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cierres)): ?>
                            <tr><td colspan="8" class="text-center">No hay cierres registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($cierres as $cierre): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($cierre['fecha_apertura'])) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($cierre['fecha_cierre'])) ?></td>
                                    <td><?= htmlspecialchars($cierre['usuario_apertura']) ?></td>
                                    <td><?= htmlspecialchars($cierre['usuario_cierre']) ?></td>
                                    <td>$<?= number_format($cierre['monto_inicial'], 2) ?></td>
                                    <td>$<?= number_format($cierre['monto_esperado'], 2) ?></td>
                                    <td>$<?= number_format($cierre['monto_contado'], 2) ?></td>
                                    <td class="<?= $cierre['diferencia'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        $<?= number_format($cierre['diferencia'], 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Abrir Caja -->
<div class="modal fade" id="modalAbrirCaja" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Abrir Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?modulo=caja&accion=abrir" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="monto_inicial" class="form-label">Monto Inicial *</label>
                        <input type="number" step="0.01" name="monto_inicial" id="monto_inicial" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="observaciones_apertura" class="form-label">Observaciones</label>
                        <textarea name="observaciones" id="observaciones_apertura" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Abrir Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cerrar Caja -->
<div class="modal fade" id="modalCerrarCaja" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cerrar Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?modulo=caja&accion=cerrar" method="POST">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <p><strong>Monto Inicial:</strong> $<?= number_format($caja_actual['monto_inicial'] ?? 0, 2) ?></p>
                        <p><strong>Monto Esperado (calculado):</strong> $<?= number_format($caja_actual['monto_esperado'] ?? 0, 2) ?></p>
                    </div>
                    <div class="mb-3">
                        <label for="monto_contado" class="form-label">Monto Contado *</label>
                        <input type="number" step="0.01" name="monto_contado" id="monto_contado" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="observaciones_cierre" class="form-label">Observaciones</label>
                        <textarea name="observaciones" id="observaciones_cierre" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Cerrar Caja</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>