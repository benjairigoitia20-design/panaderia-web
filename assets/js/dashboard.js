// ============================================
// DASHBOARD - GRÁFICOS CON DATOS REALES
// ============================================

let ventasChart = null;
let categoriasChart = null;

document.addEventListener('DOMContentLoaded', function() {
    cargarDatosDashboard();
});

function cargarDatosDashboard() {
    fetch('index.php?modulo=reportes&accion=datosDashboardJson')
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta');
            return response.json();
        })
        .then(data => {
            actualizarEstadisticas(data.stats);
            crearGraficos(data.graficos);
            cargarActividades();
        })
        .catch(error => {
            console.error('Error al cargar datos:', error);
            usarDatosEjemplo();
        });
}

function actualizarEstadisticas(stats) {
    const elementos = [
        { selector: '.stat-card:nth-child(1) .stat-value', valor: '$' + (stats.ventas_dia || '0.00') },
        { selector: '.stat-card:nth-child(2) .stat-value', valor: '$' + (stats.ventas_mes || '0.00') },
        { selector: '.stat-card:nth-child(3) .stat-value', valor: stats.pedidos_pendientes || 0 },
        { selector: '.stat-card:nth-child(4) .stat-value', valor: stats.stock_bajo || 0 },
        { selector: '.stat-card:nth-child(5) .stat-value', valor: stats.produccion_dia || 0 },
        { selector: '.stat-card:nth-child(6) .stat-value', valor: '$' + (stats.caja_actual || '0.00') }
    ];

    elementos.forEach((item) => {
        const el = document.querySelector(item.selector);
        if (el) {
            el.textContent = item.valor;
        }
    });
}

function crearGraficos(graficos) {
    // Gráfico de Ventas Semanales
    const ctx1 = document.getElementById('ventasChart');
    if (ctx1) {
        if (ventasChart) ventasChart.destroy();
        
        ventasChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: graficos.ventas_semana?.labels || ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Ventas',
                    data: graficos.ventas_semana?.values || [0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#6C3A2A',
                    backgroundColor: 'rgba(108, 58, 42, 0.08)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6C3A2A',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Gráfico de Ventas por Categoría
    const ctx2 = document.getElementById('categoriasChart');
    if (ctx2) {
        if (categoriasChart) categoriasChart.destroy();
        
        const labels = graficos.ventas_categorias?.labels || ['Sin datos'];
        const values = graficos.ventas_categorias?.values || [1];
        const colores = graficos.ventas_categorias?.colores || ['#6C3A2A'];
        
        categoriasChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colores,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: { size: 10, weight: '500' },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? (context.parsed / total * 100).toFixed(1) : 0;
                                return context.label + ': $' + context.parsed.toFixed(2) + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }
}

function cargarActividades() {
    // Versión simplificada - solo muestra mensaje
    const tbody = document.getElementById('ultimasActividades');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted">
                    <i class="bi bi-info-circle"></i> Las actividades se registrarán automáticamente
                </td>
            </tr>
        `;
    }
}

function usarDatosEjemplo() {
    const stats = {
        ventas_dia: '0.00',
        ventas_mes: '0.00',
        pedidos_pendientes: 0,
        stock_bajo: 0,
        produccion_dia: 0,
        caja_actual: '0.00'
    };
    const graficos = {
        ventas_semana: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            values: [0, 0, 0, 0, 0, 0, 0]
        },
        ventas_categorias: {
            labels: ['Panes', 'Pastelería', 'Facturas', 'Galletas', 'Otros'],
            values: [0, 0, 0, 0, 0],
            colores: ['#6C3A2A', '#D4A574', '#E8C99B', '#8B5A4A', '#A8A8A8']
        }
    };
    actualizarEstadisticas(stats);
    crearGraficos(graficos);
}