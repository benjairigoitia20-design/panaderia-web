<?php
require_once __DIR__ . '/../models/Reporte.php';
require_once __DIR__ . '/../includes/funciones.php';

class ReportesController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Reporte();
    }

    private function verificarPermiso() {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
    }

    public function index() {
        $this->verificarPermiso();
        $datos = $this->modelo->datosDashboard();
        include 'includes/header.php';
        include 'views/dashboard/index.php';
        include 'includes/footer.php';
    }

    public function ventas() {
        $this->verificarPermiso();
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        $ventas_periodo = $this->modelo->ventasPorPeriodo($fecha_inicio, $fecha_fin);
        $ventas_productos = $this->modelo->ventasPorProducto($fecha_inicio, $fecha_fin);
        $ventas_categorias = $this->modelo->ventasPorCategoria($fecha_inicio, $fecha_fin);
        $ventas_pagos = $this->modelo->ventasPorMedioPago($fecha_inicio, $fecha_fin);
        $resumen = $this->modelo->resumenGeneral($fecha_inicio, $fecha_fin);
        
        include 'includes/header.php';
        include 'views/reportes/ventas.php';
        include 'includes/footer.php';
    }

    public function inventario() {
        $this->verificarPermiso();
        $productos = $this->modelo->stockProductos();
        $ingredientes = $this->modelo->stockIngredientes();
        $stock_bajo_productos = $this->modelo->stockBajoProductos();
        $stock_bajo_ingredientes = $this->modelo->stockBajoIngredientes();
        $valorizacion = $this->modelo->valorizacionInventario();
        
        include 'includes/header.php';
        include 'views/reportes/inventario.php';
        include 'includes/footer.php';
    }

    public function produccion() {
        $this->verificarPermiso();
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        $produccion_periodo = $this->modelo->produccionPorPeriodo($fecha_inicio, $fecha_fin);
        $produccion_productos = $this->modelo->produccionPorProducto($fecha_inicio, $fecha_fin);
        
        include 'includes/header.php';
        include 'views/reportes/produccion.php';
        include 'includes/footer.php';
    }

    public function mermas() {
        $this->verificarPermiso();
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        
        $mermas_periodo = $this->modelo->mermasPorPeriodo($fecha_inicio, $fecha_fin);
        $mermas_motivos = $this->modelo->mermasPorMotivo($fecha_inicio, $fecha_fin);
        $mermas_productos = $this->modelo->mermasPorProducto($fecha_inicio, $fecha_fin);
        $mermas_ingredientes = $this->modelo->mermasPorIngrediente($fecha_inicio, $fecha_fin);
        
        include 'includes/header.php';
        include 'views/reportes/mermas.php';
        include 'includes/footer.php';
    }

    public function rentabilidad() {
        $this->verificarPermiso();
        $rentabilidad = $this->modelo->rentabilidadPorProducto();
        $rentabilidad_categoria = $this->modelo->rentabilidadPorCategoria();
        
        include 'includes/header.php';
        include 'views/reportes/rentabilidad.php';
        include 'includes/footer.php';
    }

    // ============================================================
    // NUEVO MÉTODO PARA DATOS DEL DASHBOARD EN JSON
    // ============================================================
    public function datosDashboardJson() {
        $this->verificarPermiso();
        
        $datos = $this->modelo->datosDashboard();
        
        // Obtener ventas de la semana para el gráfico
        $fecha_inicio = date('Y-m-d', strtotime('-6 days'));
        $fecha_fin = date('Y-m-d');
        $ventas_semana = $this->modelo->ventasPorPeriodo($fecha_inicio, $fecha_fin);
        
        // Preparar datos para el gráfico de ventas semanales
        $labels = [];
        $values = [];
        $dias_semana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        
        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            $labels[] = $dias_semana[date('N', strtotime($fecha)) - 1];
            $found = false;
            foreach ($ventas_semana as $v) {
                if ($v['fecha'] == $fecha) {
                    $values[] = floatval($v['total_ventas']);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $values[] = 0;
            }
        }
        
        // Obtener ventas por categoría
        $ventas_categorias = $this->modelo->ventasPorCategoria($fecha_inicio, $fecha_fin);
        $categorias_labels = [];
        $categorias_values = [];
        $colores = ['#6C3A2A', '#D4A574', '#E8C99B', '#8B5A4A', '#A8A8A8', '#2D8B46', '#E8A838', '#C0392B'];
        $categorias_colores = [];
        
        $i = 0;
        foreach ($ventas_categorias as $cat) {
            if ($i < 8) {
                $categorias_labels[] = $cat['categoria'];
                $categorias_values[] = floatval($cat['total_vendido']);
                $categorias_colores[] = $colores[$i % count($colores)];
                $i++;
            }
        }
        
        // Si no hay datos, usar valores de ejemplo
        if (empty($categorias_labels)) {
            $categorias_labels = ['Panes', 'Pastelería', 'Facturas', 'Galletas', 'Otros'];
            $categorias_values = [0, 0, 0, 0, 0];
            $categorias_colores = ['#6C3A2A', '#D4A574', '#E8C99B', '#8B5A4A', '#A8A8A8'];
        }
        
        $response = [
            'stats' => [
                'ventas_dia' => number_format($datos['ventas_dia'] ?? 0, 2),
                'ventas_mes' => number_format($datos['ventas_mes'] ?? 0, 2),
                'pedidos_pendientes' => $datos['pedidos_pendientes'] ?? 0,
                'stock_bajo' => ($datos['stock_bajo'] ?? 0) + ($datos['ingredientes_bajo'] ?? 0),
                'produccion_dia' => number_format($datos['produccion_dia'] ?? 0),
                'caja_actual' => number_format($datos['caja_actual'] ?? 0, 2)
            ],
            'graficos' => [
                'ventas_semana' => [
                    'labels' => $labels,
                    'values' => $values
                ],
                'ventas_categorias' => [
                    'labels' => $categorias_labels,
                    'values' => $categorias_values,
                    'colores' => $categorias_colores
                ]
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function ultimasActividades() {
        $this->verificarPermiso();
        $actividades = $this->modelo->obtenerUltimasActividades(10);
        header('Content-Type: application/json');
        echo json_encode($actividades);
    }
}
?>