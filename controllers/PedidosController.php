<?php
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../includes/funciones.php';

class PedidosController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Pedido();
    }

    private function verificarPermiso($escritura = false) {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
        if ($escritura && !esAdmin() && !tieneRol('encargado') && !tieneRol('vendedor')) {
            setMensaje('danger', 'No tienes permiso para realizar esta acción.');
            redirigir('index.php?modulo=pedidos&accion=index');
        }
    }

    public function index() {
        $this->verificarPermiso(false);
        $estado = $_GET['estado'] ?? 'todos';
        $pedidos = $this->modelo->obtenerTodos($estado);
        $estadisticas = $this->modelo->obtenerEstadisticas();
        $entregas_hoy = $this->modelo->obtenerEntregasHoy();
        
        include 'includes/header.php';
        include 'views/pedidos/index.php';
        include 'includes/footer.php';
    }

    public function crear() {
        $this->verificarPermiso(true);
        $productoModel = new Producto();
        $productos = $productoModel->obtenerTodos(true);
        $clienteModel = new Cliente();
        $clientes = $clienteModel->obtenerTodos(true);
        
        include 'includes/header.php';
        include 'views/pedidos/crear.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso(true);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=pedidos&accion=crear');
        }

        $productos = json_decode($_POST['productos'] ?? '[]', true);
        if (empty($productos)) {
            setMensaje('danger', 'Debes agregar al menos un producto.');
            redirigir('index.php?modulo=pedidos&accion=crear');
        }

        $cliente_id = intval($_POST['cliente_id'] ?? 0);
        if (!$cliente_id) {
            setMensaje('danger', 'Debes seleccionar un cliente.');
            redirigir('index.php?modulo=pedidos&accion=crear');
        }

        $subtotal = floatval($_POST['subtotal'] ?? 0);
        $descuento = floatval($_POST['descuento'] ?? 0);
        $total = floatval($_POST['total'] ?? 0);
        $senia = floatval($_POST['senia'] ?? 0);
        $fecha_entrega = $_POST['fecha_entrega'] ?? date('Y-m-d', strtotime('+2 days'));
        $hora_entrega = $_POST['hora_entrega'] ?? null;

        if ($total <= 0) {
            setMensaje('danger', 'El total debe ser mayor a 0.');
            redirigir('index.php?modulo=pedidos&accion=crear');
        }

        try {
            $datos = [
                'cliente_id' => $cliente_id,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'senia' => $senia,
                'fecha_entrega' => $fecha_entrega,
                'hora_entrega' => $hora_entrega,
                'productos' => $productos,
                'observaciones' => sanitizar($_POST['observaciones'] ?? '')
            ];

            $pedido_id = $this->modelo->crear($datos);
            
            if ($pedido_id) {
                setMensaje('success', 'Pedido creado correctamente. Número: ' . $this->modelo->generarNumero());
                redirigir('index.php?modulo=pedidos&accion=ver&id=' . $pedido_id);
            } else {
                throw new Exception("Error al crear el pedido");
            }

        } catch (Exception $e) {
            setMensaje('danger', 'Error al crear el pedido: ' . $e->getMessage());
            redirigir('index.php?modulo=pedidos&accion=crear');
        }
    }

    public function ver() {
        $this->verificarPermiso(false);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=pedidos&accion=index');
        }
        
        $pedido = $this->modelo->obtenerPorId($id);
        if (!$pedido) {
            setMensaje('danger', 'Pedido no encontrado.');
            redirigir('index.php?modulo=pedidos&accion=index');
        }
        
        $detalles = $this->modelo->obtenerDetalles($id);
        $seguimiento = $this->modelo->obtenerSeguimiento($id);
        
        include 'includes/header.php';
        include 'views/pedidos/ver.php';
        include 'includes/footer.php';
    }

    public function cambiarEstado() {
        $this->verificarPermiso(true);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=pedidos&accion=index');
        }

        $id = intval($_POST['id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        $observacion = sanitizar($_POST['observacion'] ?? '');

        if (!$id || !$estado) {
            setMensaje('danger', 'Datos incompletos.');
            redirigir('index.php?modulo=pedidos&accion=index');
        }

        try {
            if ($this->modelo->actualizarEstado($id, $estado, $observacion)) {
                setMensaje('success', 'Estado del pedido actualizado correctamente.');
            } else {
                throw new Exception("Error al actualizar el estado");
            }
        } catch (Exception $e) {
            setMensaje('danger', 'Error: ' . $e->getMessage());
        }
        
        redirigir('index.php?modulo=pedidos&accion=ver&id=' . $id);
    }

    public function registrarPago() {
        $this->verificarPermiso(true);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=pedidos&accion=index');
        }

        $id = intval($_POST['id'] ?? 0);
        $monto = floatval($_POST['monto'] ?? 0);

        if (!$id || $monto <= 0) {
            setMensaje('danger', 'Datos incompletos.');
            redirigir('index.php?modulo=pedidos&accion=index');
        }

        try {
            if ($this->modelo->registrarPago($id, $monto)) {
                setMensaje('success', 'Pago registrado correctamente.');
            } else {
                throw new Exception("Error al registrar el pago");
            }
        } catch (Exception $e) {
            setMensaje('danger', 'Error: ' . $e->getMessage());
        }
        
        redirigir('index.php?modulo=pedidos&accion=ver&id=' . $id);
    }

    public function cancelar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $motivo = sanitizar($_GET['motivo'] ?? 'Cancelado por usuario');
        
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=pedidos&accion=index');
        }

        try {
            if ($this->modelo->cancelar($id, $motivo)) {
                setMensaje('success', 'Pedido cancelado correctamente.');
            } else {
                throw new Exception("Error al cancelar el pedido");
            }
        } catch (Exception $e) {
            setMensaje('danger', 'Error: ' . $e->getMessage());
        }
        
        redirigir('index.php?modulo=pedidos&accion=index');
    }

    public function calendario() {
        $this->verificarPermiso(false);
        $fecha = $_GET['fecha'] ?? date('Y-m-d');
        $pedidos = $this->modelo->obtenerPorFecha($fecha);
        
        include 'includes/header.php';
        include 'views/pedidos/calendario.php';
        include 'includes/footer.php';
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
}
?>