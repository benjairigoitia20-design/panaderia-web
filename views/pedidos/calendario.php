<div class="container mt-4">
    <h2>Calendario de Entregas</h2>
    
    <?php if ($mensaje = getMensaje()): ?>
        <div class="alert alert-<?= $mensaje['tipo'] ?>"><?= $mensaje['texto'] ?></div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" class="row g-3">
                <input type="hidden" name="modulo" value="pedidos">
                <input type="hidden" name="accion" value="calendario">
                <div class="col-md-8">
                    <input type="date" name="fecha" class="form-control" value="<?= $fecha ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Ver</button>
                    <a href="index.php?modulo=pedidos&accion=calendario&fecha=<?= date('Y-m-d') ?>" class="btn btn-secondary">Hoy</a>
                </div>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group" role="group">
                <a href="index.php?modulo=pedidos&accion=calendario&fecha=<?= date('Y-m-d', strtotime($fecha . ' -1 day')) ?>" class="btn btn-outline-primary">←</a>
                <span class="btn btn-primary disabled">
                    <?= date('d/m/Y', strtotime($fecha)) ?>
                </span>
                <a href="index.php?modulo=pedidos&accion=calendario&fecha=<?= date('Y-m-d', strtotime($fecha . ' +1 day')) ?>" class="btn btn-outline-primary">→</a>
            </div>
        </div>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info">No hay pedidos programados para esta fecha.</div>
    <?php else: ?>
        <div class="row">
            <?php 
            $contador = 0;
            foreach ($pedidos as $pedido): 
                $contador++;
                $color = [
                    'pendiente' => 'warning',
                    'confirmado' => 'info',
                    'en_produccion' => 'danger',
                    'listo' => 'success'
                ][$pedido['estado']] ?? 'secondary';
            ?>
                <div class="col-md-4 mb-3">
                    <div class="card border-<?= $color ?> h-100">
                        <div class="card-header bg-<?= $color ?> text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong><?= htmlspecialchars($pedido['numero']) ?></strong>
                                <span class="badge bg-light text-dark">
                                    <?= ucfirst(str_replace('_', ' ', $pedido['estado'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?= htmlspecialchars($pedido['cliente_nombre'] ?? '') ?>
                                <?= htmlspecialchars($pedido['cliente_apellido'] ?? '') ?>
                            </h5>
                            <p class="card-text">
                                <strong>Teléfono:</strong> <?= htmlspecialchars($pedido['cliente_telefono'] ?? '-') ?><br>
                                <strong>Hora:</strong> <?= $pedido['hora_entrega'] ? date('H:i', strtotime($pedido['hora_entrega'])) : 'No especificada' ?><br>
                                <strong>Total:</strong> $<?= number_format($pedido['total'], 2) ?><br>
                                <strong>Saldo:</strong> $<?= number_format($pedido['saldo'], 2) ?>
                            </p>
                            <?php if (!empty($pedido['observaciones'])): ?>
                                <p class="card-text"><small class="text-muted"><?= htmlspecialchars($pedido['observaciones']) ?></small></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="index.php?modulo=pedidos&accion=ver&id=<?= $pedido['id'] ?>" class="btn btn-sm btn-info">Ver Detalle</a>
                            <?php if ($pedido['estado'] != 'entregado' && $pedido['estado'] != 'cancelado'): ?>
                                <a href="index.php?modulo=pedidos&accion=cambiarEstado&id=<?= $pedido['id'] ?>&estado=entregado" class="btn btn-sm btn-success" onclick="return confirm('¿Marcar como entregado?')">Entregar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php if ($contador % 3 == 0): ?>
                    <div class="w-100"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>