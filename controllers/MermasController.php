<?php
require_once __DIR__ . '/../models/Ingrediente.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/UnidadMedida.php';
require_once __DIR__ . '/../includes/funciones.php';

class MermasController {
    private $pdo;

    public function __construct() {
        $this->pdo = conectarDB();
    }

    private function verificarPermiso($escritura = false) {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
        if ($escritura && !esAdmin() && !tieneRol('panadero') && !tieneRol('encargado')) {
            setMensaje('danger', 'No tienes permiso para realizar esta acción.');
            redirigir('index.php?modulo=mermas&accion=index');
        }
    }

    public function index() {
        $this->verificarPermiso(false);
        $sql = "SELECT m.*, 
                p.nombre as producto_nombre,
                i.nombre as ingrediente_nombre,
                u.nombre as unidad_nombre,
                us.nombre as usuario_nombre
                FROM mermas m
                LEFT JOIN productos p ON m.producto_id = p.id
                LEFT JOIN ingredientes i ON m.ingrediente_id = i.id
                LEFT JOIN unidades_medida u ON m.unidad_medida_id = u.id
                LEFT JOIN usuarios us ON m.usuario_id = us.id
                ORDER BY m.fecha DESC, m.id DESC
                LIMIT 100";
        $stmt = $this->pdo->query($sql);
        $mermas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include 'includes/header.php';
        include 'views/mermas/index.php';
        include 'includes/footer.php';
    }

    public function crear() {
        $this->verificarPermiso(true);
        $unidadModel = new UnidadMedida();
        $unidades = $unidadModel->obtenerTodos(true);
        $productoModel = new Producto();
        $productos = $productoModel->obtenerTodos(true);
        $ingredienteModel = new Ingrediente();
        $ingredientes = $ingredienteModel->obtenerTodos(true);
        
        include 'includes/header.php';
        include 'views/mermas/crear.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=mermas&accion=crear');
        }

        $tipo = $_POST['tipo'] ?? '';
        $tipo_item = $_POST['tipo_item'] ?? 'ingrediente';
        $cantidad = floatval($_POST['cantidad'] ?? 0);
        $unidad_medida_id = intval($_POST['unidad_medida_id'] ?? 0);
        $fecha = $_POST['fecha'] ?? date('Y-m-d');

        if (!$tipo || $cantidad <= 0 || !$unidad_medida_id) {
            setMensaje('danger', 'Todos los campos son obligatorios.');
            redirigir('index.php?modulo=mermas&accion=crear');
        }

        $producto_id = null;
        $ingrediente_id = null;
        $costo_estimado = 0;

        if ($tipo_item == 'ingrediente') {
            $ingrediente_id = intval($_POST['ingrediente_id'] ?? 0);
            if (!$ingrediente_id) {
                setMensaje('danger', 'Debes seleccionar un ingrediente.');
                redirigir('index.php?modulo=mermas&accion=crear');
            }
            // Obtener costo del ingrediente
            $ingredienteModel = new Ingrediente();
            $ingrediente = $ingredienteModel->obtenerPorId($ingrediente_id);
            $costo_estimado = $cantidad * ($ingrediente['costo_unitario'] ?? 0);
            
            // Descontar stock
            $nuevo_stock = $ingrediente['stock_actual'] - $cantidad;
            $ingredienteModel->actualizarStock($ingrediente_id, $nuevo_stock);
            
        } else {
            $producto_id = intval($_POST['producto_id'] ?? 0);
            if (!$producto_id) {
                setMensaje('danger', 'Debes seleccionar un producto.');
                redirigir('index.php?modulo=mermas&accion=crear');
            }
            // Obtener costo del producto
            $productoModel = new Producto();
            $producto = $productoModel->obtenerPorId($producto_id);
            $costo_estimado = $cantidad * ($producto['precio'] ?? 0);
            
            // Descontar stock
            $nuevo_stock = $producto['stock'] - $cantidad;
            $update = $this->pdo->prepare("UPDATE productos SET stock = ? WHERE id = ?");
            $update->execute([$nuevo_stock, $producto_id]);
        }

        $sql = "INSERT INTO mermas (tipo, producto_id, ingrediente_id, cantidad, unidad_medida_id, 
                                    costo_estimado, fecha, usuario_id, motivo, observacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            $tipo,
            $producto_id,
            $ingrediente_id,
            $cantidad,
            $unidad_medida_id,
            $costo_estimado,
            $fecha,
            $_SESSION['usuario_id'],
            sanitizar($_POST['motivo'] ?? ''),
            sanitizar($_POST['observacion'] ?? '')
        ]);

        if ($result) {
            setMensaje('success', 'Merma registrada correctamente.');
        } else {
            setMensaje('danger', 'Error al registrar la merma.');
        }
        redirigir('index.php?modulo=mermas&accion=index');
    }
}
?>