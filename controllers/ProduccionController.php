<?php
require_once __DIR__ . '/../models/OrdenProduccion.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Receta.php';
require_once __DIR__ . '/../models/Ingrediente.php';
require_once __DIR__ . '/../includes/funciones.php';

class ProduccionController {
    private $modelo;

    public function __construct() {
        $this->modelo = new OrdenProduccion();
    }

    private function verificarPermiso($escritura = false) {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
        if ($escritura && !esAdmin() && !tieneRol('panadero') && !tieneRol('encargado')) {
            setMensaje('danger', 'No tienes permiso para realizar esta acción.');
            redirigir('index.php?modulo=produccion&accion=index');
        }
    }

    public function index() {
        $this->verificarPermiso(false);
        $ordenes = $this->modelo->obtenerTodos(false);
        include 'includes/header.php';
        include 'views/produccion/index.php';
        include 'includes/footer.php';
    }

    public function crear() {
        $this->verificarPermiso(true);
        $productoModel = new Producto();
        $productos = $productoModel->obtenerTodos(true);
        $recetaModel = new Receta();
        $recetas = $recetaModel->obtenerTodos(true);
        
        // Obtener usuarios (solo admin o encargado pueden asignar)
        if (esAdmin() || tieneRol('encargado')) {
            $stmt = $this->pdo->query("SELECT id, nombre FROM usuarios WHERE activo = 1 ORDER BY nombre");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $usuarios = [];
        }
        
        include 'includes/header.php';
        include 'views/produccion/crear.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=produccion&accion=crear');
        }

        $producto_id = intval($_POST['producto_id'] ?? 0);
        $receta_id = intval($_POST['receta_id'] ?? 0);
        $cantidad = floatval($_POST['cantidad_planificada'] ?? 0);
        $fecha = $_POST['fecha_produccion'] ?? date('Y-m-d');

        if (!$producto_id || !$receta_id || $cantidad <= 0) {
            setMensaje('danger', 'Todos los campos son obligatorios.');
            redirigir('index.php?modulo=produccion&accion=crear');
        }

        // Verificar que la receta corresponda al producto
        $recetaModel = new Receta();
        $receta = $recetaModel->obtenerPorId($receta_id);
        if (!$receta || $receta['producto_id'] != $producto_id) {
            setMensaje('danger', 'La receta no corresponde al producto seleccionado.');
            redirigir('index.php?modulo=produccion&accion=crear');
        }

        $datos = [
            'producto_id' => $producto_id,
            'receta_id' => $receta_id,
            'cantidad_planificada' => $cantidad,
            'fecha_produccion' => $fecha,
            'responsable_id' => $_SESSION['usuario_id'],
            'observaciones' => sanitizar($_POST['observaciones'] ?? '')
        ];

        if ($this->modelo->crear($datos)) {
            setMensaje('success', 'Orden de producción creada correctamente.');
        } else {
            setMensaje('danger', 'Error al crear la orden de producción.');
        }
        redirigir('index.php?modulo=produccion&accion=index');
    }

    public function ver() {
        $this->verificarPermiso(false);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=produccion&accion=index');
        }
        
        $orden = $this->modelo->obtenerPorId($id);
        if (!$orden) {
            setMensaje('danger', 'Orden no encontrada.');
            redirigir('index.php?modulo=produccion&accion=index');
        }
        
        $ingredientes = $this->modelo->obtenerIngredientes($id);
        $faltantes = $this->modelo->verificarStock($id);
        
        include 'includes/header.php';
        include 'views/produccion/ver.php';
        include 'includes/footer.php';
    }

    public function iniciar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=produccion&accion=index');
        }
        
        // Verificar stock antes de iniciar
        $faltantes = $this->modelo->verificarStock($id);
        if (!empty($faltantes)) {
            $mensaje = "No se puede iniciar la producción. Faltan ingredientes:\n";
            foreach ($faltantes as $falta) {
                $mensaje .= "- {$falta['ingrediente']}: {$falta['faltante']} {$falta['unidad']}\n";
            }
            setMensaje('danger', nl2br($mensaje));
            redirigir('index.php?modulo=produccion&accion=ver&id=' . $id);
        }
        
        if ($this->modelo->iniciarProduccion($id)) {
            setMensaje('success', 'Producción iniciada correctamente.');
        } else {
            setMensaje('danger', 'Error al iniciar la producción.');
        }
        redirigir('index.php?modulo=produccion&accion=index');
    }

    public function finalizar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=produccion&accion=index');
        }

        $id = intval($_POST['id'] ?? 0);
        $cantidad_producida = floatval($_POST['cantidad_producida'] ?? 0);
        $observaciones = sanitizar($_POST['observaciones'] ?? '');

        if (!$id || $cantidad_producida <= 0) {
            setMensaje('danger', 'Cantidad producida es obligatoria.');
            redirigir('index.php?modulo=produccion&accion=ver&id=' . $id);
        }

        if ($this->modelo->finalizarProduccion($id, $cantidad_producida, $observaciones)) {
            setMensaje('success', 'Producción finalizada correctamente. Se ha actualizado el stock.');
        } else {
            setMensaje('danger', 'Error al finalizar la producción.');
        }
        redirigir('index.php?modulo=produccion&accion=index');
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
        redirigir('index.php?modulo=produccion&accion=index');
    }

    public function obtenerRecetas() {
        // Para AJAX - obtener recetas por producto
        if (!estaLogueado()) {
            echo json_encode([]);
            return;
        }
        
        $producto_id = isset($_GET['producto_id']) ? intval($_GET['producto_id']) : 0;
        if (!$producto_id) {
            echo json_encode([]);
            return;
        }
        
        $recetaModel = new Receta();
        $recetas = $recetaModel->obtenerTodos(true);
        $resultado = [];
        
        foreach ($recetas as $receta) {
            if ($receta['producto_id'] == $producto_id) {
                $resultado[] = [
                    'id' => $receta['id'],
                    'nombre' => $receta['nombre'],
                    'rendimiento' => $receta['rendimiento'],
                    'unidad_rendimiento' => $receta['unidad_rendimiento']
                ];
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultado);
    }
}
?>