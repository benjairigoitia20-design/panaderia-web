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
        .then(leerRespuesta)
        .then(data => {
            actualizarEstadisticas(data.stats);
            crearGraficos(data.graficos);
        })
        .catch(error => {
            console.error('Error al cargar datos del dashboard:', error);
            mostrarErrorDashboard(error);
        });

    cargarActividades();
}

function leerRespuesta(response) {
    return response.json().then(data => {
        if (!response.ok) {
            throw new Error(data.error || 'El servidor no pudo completar la solicitud.');
        }
        return data;
    }).catch(error => {
        if (error instanceof SyntaxError) {
            throw new Error('La respuesta del servidor no tiene un formato válido.');
        }
        throw error;
    });
}

function actualizarEstadisticas(stats) {
    const valores = [
        formatearMoneda(stats.ventas_dia),
        formatearMoneda(stats.ventas_mes),
        formatearNumero(stats.pedidos_pendientes),
        formatearNumero(stats.stock_bajo),
        formatearNumero(stats.produccion_dia),
        formatearMoneda(stats.caja_actual)
    ];
    const etiquetas = [
        'Ventas registradas hoy',
        'Ventas del mes en curso',
        'Pedidos sin entregar',
        'Productos e ingredientes bajo mínimo',
        'Unidades terminadas hoy',
        'Saldo esperado de la caja abierta'
    ];
    const tarjetas = document.querySelectorAll('.stats-grid .stat-card');

    tarjetas.forEach((tarjeta, index) => {
        const valor = tarjeta.querySelector('.stat-value');
        const detalle = tarjeta.querySelector('.stat-change');
        if (valor) valor.textContent = valores[index] ?? '—';
        if (detalle) detalle.textContent = etiquetas[index] ?? '';
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
                labels: graficos.ventas_semana.labels,
                datasets: [{
                    label: 'Ventas',
                    data: graficos.ventas_semana.values,
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
        
        const labels = graficos.ventas_categorias.labels;
        const values = graficos.ventas_categorias.values;
        const colores = graficos.ventas_categorias.colores;
        
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
    fetch('index.php?modulo=reportes&accion=ultimasActividades')
        .then(leerRespuesta)
        .then(renderizarActividades)
        .catch(error => {
            console.error('Error al cargar las actividades:', error);
            renderizarMensajeActividad('No se pudieron cargar las actividades.');
        });
}

function renderizarActividades(actividades) {
    const tbody = document.getElementById('ultimasActividades');
    if (!tbody) return;

    tbody.replaceChildren();
    if (!Array.isArray(actividades) || actividades.length === 0) {
        renderizarMensajeActividad('Todavía no hay actividades registradas.');
        return;
    }

    actividades.forEach(actividad => {
        const fila = document.createElement('tr');
        [actividad.fecha, actividad.usuario || 'Sistema', actividad.accion, actividad.modulo]
            .forEach(valor => {
                const celda = document.createElement('td');
                celda.textContent = valor || '—';
                fila.appendChild(celda);
            });
        tbody.appendChild(fila);
    });
}

function renderizarMensajeActividad(mensaje) {
    const tbody = document.getElementById('ultimasActividades');
    if (!tbody) return;

    const fila = document.createElement('tr');
    const celda = document.createElement('td');
    celda.colSpan = 4;
    celda.className = 'text-center text-muted';
    celda.textContent = mensaje;
    fila.appendChild(celda);
    tbody.replaceChildren(fila);
}

function mostrarErrorDashboard(error) {
    document.querySelectorAll('.stats-grid .stat-value').forEach(valor => {
        valor.textContent = '—';
    });
    document.querySelectorAll('.stats-grid .stat-change').forEach(detalle => {
        detalle.textContent = 'No disponible';
    });
    document.querySelectorAll('.charts-grid .chart-card').forEach(tarjeta => {
        const aviso = document.createElement('p');
        aviso.className = 'text-muted text-center mt-4';
        aviso.textContent = error.message;
        tarjeta.appendChild(aviso);
        const canvas = tarjeta.querySelector('canvas');
        if (canvas) canvas.hidden = true;
    });
}

function formatearMoneda(valor) {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: 'ARS',
        minimumFractionDigits: 2
    }).format(Number(valor) || 0);
}

function formatearNumero(valor) {
    return new Intl.NumberFormat('es-AR', {
        maximumFractionDigits: 2
    }).format(Number(valor) || 0);
}