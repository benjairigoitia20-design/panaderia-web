<?php
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../includes/funciones.php';

class VentasController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Venta();
    }

    private function verificarPermiso($escritura = false) {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
        if ($escritura && !esAdmin() && !tieneRol('vendedor') && !tieneRol('encargado')) {
            setMensaje('danger', 'No tienes permiso para realizar ventas.');
            redirigir('index.php?modulo=ventas&accion=index');
        }
    }

    public function index() {
        $this->verificarPermiso(false);
        $ventas = $this->modelo->obtenerTodas(50);
        $total_dia = $this->modelo->obtenerTotalDelDia();
        include 'includes/header.php';
        include 'views/ventas/index.php';
        include 'includes/footer.php';
    }

    public function nueva() {
        $this->verificarPermiso(true);
        
        // Verificar que haya caja abierta
        $caja = $this->modelo->obtenerCajaAbierta();
        if (!$caja && esAdmin()) {
            // Admin puede vender sin caja abierta
        } elseif (!$caja) {
            setMensaje('danger', 'No hay caja abierta. Solicita al encargado que abra la caja.');
            redirigir('index.php?modulo=ventas&accion=index');
        }
        
        $productoModel = new Producto();
        $productos = $productoModel->obtenerTodos(true);
        $clienteModel = new Cliente();
        $clientes = $clienteModel->obtenerTodos(true);
        
        include 'includes/header.php';
        include 'views/ventas/nueva.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso(true);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=ventas&accion=nueva');
        }

        $productos = json_decode($_POST['productos'] ?? '[]', true);
        if (empty($productos)) {
            setMensaje('danger', 'Debes agregar al menos un producto.');
            redirigir('index.php?modulo=ventas&accion=nueva');
        }

        $subtotal = floatval($_POST['subtotal'] ?? 0);
        $descuento = floatval($_POST['descuento'] ?? 0);
        $total = floatval($_POST['total'] ?? 0);
        $medio_pago = $_POST['medio_pago'] ?? 'efectivo';
        $cliente_id = !empty($_POST['cliente_id']) ? intval($_POST['cliente_id']) : null;

        if ($total <= 0) {
            setMensaje('danger', 'El total debe ser mayor a 0.');
            redirigir('index.php?modulo=ventas&accion=nueva');
        }

        try {
            $datos = [
                'cliente_id' => $cliente_id,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'medio_pago' => $medio_pago,
                'productos' => $productos,
                'observaciones' => sanitizar($_POST['observaciones'] ?? '')
            ];

            $venta_id = $this->modelo->crear($datos);
            
            if ($venta_id) {
                setMensaje('success', 'Venta realizada correctamente. Número: ' . $this->modelo->generarNumero());
                redirigir('index.php?modulo=ventas&accion=ver&id=' . $venta_id);
            } else {
                throw new Exception("Error al crear la venta");
            }

        } catch (Exception $e) {
            setMensaje('danger', 'Error al realizar la venta: ' . $e->getMessage());
            redirigir('index.php?modulo=ventas&accion=nueva');
        }
    }

    public function ver() {
        $this->verificarPermiso(false);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=ventas&accion=index');
        }
        
        $venta = $this->modelo->obtenerPorId($id);
        if (!$venta) {
            setMensaje('danger', 'Venta no encontrada.');
            redirigir('index.php?modulo=ventas&accion=index');
        }
        
        $detalles = $this->modelo->obtenerDetalles($id);
        
        include 'includes/header.php';
        include 'views/ventas/ver.php';
        include 'includes/footer.php';
    }

    public function cancelar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=ventas&accion=index');
        }

        try {
            if ($this->modelo->cancelar($id)) {
                setMensaje('success', 'Venta cancelada correctamente. Stock restaurado.');
            } else {
                throw new Exception("Error al cancelar la venta");
            }
        } catch (Exception $e) {
            setMensaje('danger', 'Error al cancelar la venta: ' . $e->getMessage());
        }
        
        redirigir('index.php?modulo=ventas&accion=index');
    }

    public function buscarProductos() {
        if (!estaLogueado()) {
            echo json_encode([]);
            return;
        }
        
        $termino = $_GET['termino'] ?? '';
        if (strlen($termino) < 2) {
            echo json_encode([]);
            return;
        }
        
        $productoModel = new Producto();
        $productos = $productoModel->obtenerTodos(true);
        $resultados = [];
        
        foreach ($productos as $producto) {
            if (stripos($producto['nombre'], $termino) !== false) {
                $resultados[] = [
                    'id' => $producto['id'],
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'stock' => $producto['stock'],
                    'imagen' => $producto['imagen']
                ];
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultados);
    }

    public function buscarClientes() {
        if (!estaLogueado()) {
            echo json_encode([]);
            return;
        }
        
        $termino = $_GET['termino'] ?? '';
        if (strlen($termino) < 2) {
            echo json_encode([]);
            return;
        }
        
        $clienteModel = new Cliente();
        $clientes = $clienteModel->buscar($termino);
        
        header('Content-Type: application/json');
        echo json_encode($clientes);
    }
}
?>