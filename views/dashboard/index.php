<div class="animate-in">
    <!-- ====== ESTADÍSTICAS ====== -->
    <div class="stats-grid">
        <div class="stat-card animate-in">
            <div class="stat-icon primary"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value">$0.00</div>
            <div class="stat-label">Ventas del Día</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>

        <div class="stat-card animate-in">
            <div class="stat-icon success"><i class="bi bi-calendar3"></i></div>
            <div class="stat-value">$0.00</div>
            <div class="stat-label">Ventas del Mes</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>

        <div class="stat-card animate-in">
            <div class="stat-icon warning"><i class="bi bi-clock-history"></i></div>
            <div class="stat-value">0</div>
            <div class="stat-label">Pedidos Pendientes</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>

        <div class="stat-card animate-in">
            <div class="stat-icon danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="stat-value">0</div>
            <div class="stat-label">Stock Bajo</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>

        <div class="stat-card animate-in">
            <div class="stat-icon info"><i class="bi bi-gear"></i></div>
            <div class="stat-value">0</div>
            <div class="stat-label">Producción del Día</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>

        <div class="stat-card animate-in">
            <div class="stat-icon secondary"><i class="bi bi-wallet2"></i></div>
            <div class="stat-value">$0.00</div>
            <div class="stat-label">Caja Actual</div>
            <div class="stat-change neutral">Cargando...</div>
        </div>
    </div>

    <!-- ====== GRÁFICOS ====== -->
    <div class="charts-grid">
        <div class="chart-card animate-in">
            <div class="chart-header">
                <h6><i class="bi bi-graph-up-arrow text-primary"></i> Ventas de la Semana</h6>
                <span class="chart-tag">Últimos 7 días</span>
            </div>
            <canvas id="ventasChart" height="200"></canvas>
        </div>

        <div class="chart-card animate-in">
            <div class="chart-header">
                <h6><i class="bi bi-pie-chart text-success"></i> Ventas por Categoría</h6>
                <span class="chart-tag">Distribución</span>
            </div>
            <canvas id="categoriasChart" height="200"></canvas>
        </div>
    </div>

    <!-- ====== ACCIONES RÁPIDAS ====== -->
    <div class="quick-actions">
        <a href="index.php?modulo=ventas&accion=nueva" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Nueva Venta
        </a>
        <a href="index.php?modulo=pedidos&accion=crear" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Pedido
        </a>
        <a href="index.php?modulo=produccion&accion=crear" class="btn btn-warning">
            <i class="bi bi-plus-circle"></i> Nueva Producción
        </a>
        <a href="index.php?modulo=compras&accion=crear" class="btn btn-info">
            <i class="bi bi-plus-circle"></i> Nueva Compra
        </a>
        <a href="index.php?modulo=productos&accion=crear" class="btn btn-secondary">
            <i class="bi bi-plus-circle"></i> Nuevo Producto
        </a>
    </div>

    <!-- ====== ÚLTIMAS ACTIVIDADES ====== -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="chart-card animate-in">
                <div class="chart-header">
                    <h6><i class="bi bi-clock text-secondary"></i> Últimas Actividades</h6>
                    <span class="chart-tag">Actividad reciente</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Módulo</th>
                            </tr>
                        </thead>
                        <tbody id="ultimasActividades">
                            <tr>
                                <td colspan="4" class="text-center text-muted">Cargando actividades...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ====== SCRIPTS ====== -->
<script src="assets/js/dashboard.js"></script>