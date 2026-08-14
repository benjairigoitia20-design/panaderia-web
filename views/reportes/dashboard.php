<div class="container mt-4">
    <h2>Dashboard</h2>
    
    <!-- Indicadores -->
    <div class="row mt-3">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Ventas del Día</h5>
                    <h2>$<?= number_format($datos['ventas_dia'], 2) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Ventas del Mes</h5>
                    <h2>$<?= number_format($datos['ventas_mes'], 2) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Pedidos Pendientes</h5>
                    <h2><?= $datos['pedidos_pendientes'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5>Stock Bajo</h5>
                    <h2><?= $datos['stock_bajo'] + $datos['ingredientes_bajo'] ?></h2>
                    <small>Productos: <?= $datos['stock_bajo'] ?> | Ingredientes: <?= $datos['ingredientes_bajo'] ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila -->
    <div class="row mt-2">
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Producción del Día</h5>
                    <h2><?= number_format($datos['produccion_dia']) ?></h2>
                    <small>Unidades producidas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5>Mermas del Día</h5>
                    <h2>$<?= number_format($datos['mermas_dia'], 2) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h5>Caja Actual</h5>
                    <h2>$<?= number_format($datos['caja_actual'], 2) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white" style="background-color: #6f42c1;">
                <div class="card-body">
                    <h5>Ventas Semana</h5>
                    <h2>$<?= number_format($datos['ventas_semana'], 2) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Enlaces rápidos a reportes -->
    <div class="row mt-4">
        <div class="col-12">
            <h4>Reportes Detallados</h4>
            <div class="btn-group flex-wrap" role="group">
                <a href="index.php?modulo=reportes&accion=ventas" class="btn btn-outline-primary mb-1">
                    <i class="bi bi-graph-up"></i> Ventas
                </a>
                <a href="index.php?modulo=reportes&accion=inventario" class="btn btn-outline-success mb-1">
                    <i class="bi bi-box-seam"></i> Inventario
                </a>
                <a href="index.php?modulo=reportes&accion=produccion" class="btn btn-outline-info mb-1">
                    <i class="bi bi-gear"></i> Producción
                </a>
                <a href="index.php?modulo=reportes&accion=mermas" class="btn btn-outline-warning mb-1">
                    <i class="bi bi-exclamation-triangle"></i> Mermas
                </a>
                <a href="index.php?modulo=reportes&accion=rentabilidad" class="btn btn-outline-danger mb-1">
                    <i class="bi bi-cash-stack"></i> Rentabilidad
                </a>
            </div>
        </div>
    </div>
</div>