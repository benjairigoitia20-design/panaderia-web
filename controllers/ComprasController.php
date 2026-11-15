<?php
require_once __DIR__ . '/../models/OrdenCompra.php';
require_once __DIR__ . '/../models/Proveedor.php';
require_once __DIR__ . '/../models/Ingrediente.php';
require_once __DIR__ . '/../includes/funciones.php';

class ComprasController {
    private $modelo;

    public function __construct() {
        $this->modelo = new OrdenCompra();
    }

    private function verificarPermiso($escritura = false) {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
        if ($escritura && !esAdmin() && !tieneRol('encargado')) {
            setMensaje('danger', 'No tienes permiso para realizar esta acción.');
            redirigir('index.php?modulo=compras&accion=index');
        }
    }

    public function index() {
        $this->verificarPermiso(false);
        $estado = $_GET['estado'] ?? 'todos';
        $ordenes = $this->modelo->obtenerTodos($estado);
        $estadisticas = $this->modelo->obtenerEstadisticas();
        include 'includes/header.php';
        include 'views/compras/index.php';
        include 'includes/footer.php';
    }

    public function crear() {
        $this->verificarPermiso(true);
        $proveedorModel = new Proveedor();
        $proveedores = $proveedorModel->obtenerTodos(true);
        $ingredienteModel = new Ingrediente();
        $ingredientes = $ingredienteModel->obtenerTodos(true);
        include 'includes/header.php';
        include 'views/compras/crear.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=compras&accion=crear');
        }

        $productos = json_decode($_POST['productos'] ?? '[]', true);
        if (empty($productos)) {
            setMensaje('danger', 'Debes agregar al menos un producto.');
            redirigir('index.php?modulo=compras&accion=crear');
        }

        $proveedor_id = intval($_POST['proveedor_id'] ?? 0);
        if (!$proveedor_id) {
            setMensaje('danger', 'Debes seleccionar un proveedor.');
            redirigir('index.php?modulo=compras&accion=crear');
        }

        $subtotal = floatval($_POST['subtotal'] ?? 0);
        $descuento = floatval($_POST['descuento'] ?? 0);
        $total = floatval($_POST['total'] ?? 0);
        $fecha_orden = $_POST['fecha_orden'] ?? date('Y-m-d');

        if ($total <= 0) {
            setMensaje('danger', 'El total debe ser mayor a 0.');
            redirigir('index.php?modulo=compras&accion=crear');
        }

        try {
            $datos = [
                'proveedor_id' => $proveedor_id,
                'fecha_orden' => $fecha_orden,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'productos' => $productos,
                'observaciones' => sanitizar($_POST['observaciones'] ?? '')
            ];

            $orden_id = $this->modelo->crear($datos);
            
            if ($orden_id) {
                setMensaje('success', 'Orden de compra creada correctamente.');
                redirigir('index.php?modulo=compras&accion=ver&id=' . $orden_id);
            } else {
                throw new Exception("Error al crear la orden");
            }

        } catch (Exception $e) {
            setMensaje('danger', 'Error al crear la orden: ' . $e->getMessage());
            redirigir('index.php?modulo=compras&accion=crear');
        }
    }

    public function ver() {
        $this->verificarPermiso(false);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=compras&accion=index');
        }
        
        $orden = $this->modelo->obtenerPorId($id);
        if (!$orden) {
            setMensaje('danger', 'Orden no encontrada.');
            redirigir('index.php?modulo=compras&accion=index');
        }
        
        $detalles = $this->modelo->obtenerDetalles($id);
        include 'includes/header.php';
        include 'views/compras/ver.php';
        include 'includes/footer.php';
    }

    public function recibir() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=compras&accion=index');
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=compras&accion=index');
        }

        $cantidades = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'cantidad_') === 0) {
                $detalle_id = intval(str_replace('cantidad_', '', $key));
                $cantidades[$detalle_id] = floatval($value);
            }
        }

        try {
            if ($this->modelo->recibir($id, ['cantidades' => $cantidades])) {
                setMensaje('success', 'Compra recibida correctamente. Stock actualizado.');
            } else {
                throw new Exception("Error al recibir la compra");
            }
        } catch (Exception $e) {
            setMensaje('danger', 'Error: ' . $e->getMessage());
        }
        
        redirigir('index.php?modulo=compras&accion=ver&id=' . $id);
    }

    public function cancelar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $motivo = sanitizar($_GET['motivo'] ?? 'Cancelada por usuario');
        
        if ($id && $this->modelo->cancelar($id, $motivo)) {
            setMensaje('success', 'Orden cancelada correctamente.');
        } else {
            setMensaje('danger', 'Error al cancelar la orden.');
        }
        redirigir('index.php?modulo=compras&accion=index');
    }

    public function buscarIngredientes() {
        if (!estaLogueado()) {
            echo json_encode([]);
            return;
        }
        
        $termino = $_GET['termino'] ?? '';
        if (strlen($termino) < 2) {
            echo json_encode([]);
            return;
        }
        
        $ingredienteModel = new Ingrediente();
        $ingredientes = $ingredienteModel->obtenerTodos(true);
        $resultados = [];
        
        foreach ($ingredientes as $ing) {
            if (stripos($ing['nombre'], $termino) !== false) {
                $resultados[] = [
                    'id' => $ing['id'],
                    'nombre' => $ing['nombre'],
                    'costo_unitario' => $ing['costo_unitario'],
                    'stock_actual' => $ing['stock_actual'],
                    'unidad_abreviatura' => $ing['unidad_abreviatura']
                ];
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultados);
    }
}
?>