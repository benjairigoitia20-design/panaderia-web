<?php
require_once __DIR__ . '/../models/Receta.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Ingrediente.php';
require_once __DIR__ . '/../models/UnidadMedida.php';
require_once __DIR__ . '/../includes/funciones.php';

class RecetasController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Receta();
    }

    private function verificarPermiso($escritura = false) {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
        if ($escritura && !esAdmin() && !tieneRol('panadero')) {
            setMensaje('danger', 'No tienes permiso para realizar esta acción.');
            redirigir('index.php?modulo=recetas&accion=index');
        }
    }

    public function index() {
        $this->verificarPermiso(false);
        $recetas = $this->modelo->obtenerTodos(false);
        include 'includes/header.php';
        include 'views/recetas/index.php';
        include 'includes/footer.php';
    }

    public function crear() {
        $this->verificarPermiso(true);
        $productoModel = new Producto();
        $productos = $productoModel->obtenerTodos(true);
        $ingredienteModel = new Ingrediente();
        $ingredientes = $ingredienteModel->obtenerTodos(true);
        $unidadModel = new UnidadMedida();
        $unidades = $unidadModel->obtenerTodos(true);
        include 'includes/header.php';
        include 'views/recetas/crear.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=recetas&accion=crear');
        }

        $nombre = sanitizar($_POST['nombre'] ?? '');
        if (empty($nombre) || empty($_POST['producto_id']) || empty($_POST['rendimiento'])) {
            setMensaje('danger', 'Nombre, producto y rendimiento son obligatorios.');
            redirigir('index.php?modulo=recetas&accion=crear');
        }

        if ($this->modelo->existeNombre($nombre)) {
            setMensaje('danger', 'Ya existe una receta con ese nombre.');
            redirigir('index.php?modulo=recetas&accion=crear');
        }

        // Procesar ingredientes
        $ingredientes = [];
        if (isset($_POST['ingredientes']) && is_array($_POST['ingredientes'])) {
            foreach ($_POST['ingredientes'] as $index => $ing) {
                if (!empty($ing['ingrediente_id']) && !empty($ing['cantidad']) && !empty($ing['unidad_medida_id'])) {
                    $ingredientes[] = [
                        'ingrediente_id' => intval($ing['ingrediente_id']),
                        'cantidad' => floatval($ing['cantidad']),
                        'unidad_medida_id' => intval($ing['unidad_medida_id'])
                    ];
                }
            }
        }

        if (empty($ingredientes)) {
            setMensaje('danger', 'Debes agregar al menos un ingrediente a la receta.');
            redirigir('index.php?modulo=recetas&accion=crear');
        }

        $datos = [
            'producto_id' => intval($_POST['producto_id']),
            'nombre' => $nombre,
            'rendimiento' => floatval($_POST['rendimiento']),
            'unidad_rendimiento' => sanitizar($_POST['unidad_rendimiento']),
            'tiempo_preparacion' => intval($_POST['tiempo_preparacion'] ?? 0),
            'tiempo_coccion' => intval($_POST['tiempo_coccion'] ?? 0),
            'instrucciones' => sanitizar($_POST['instrucciones'] ?? ''),
            'estado' => isset($_POST['estado']) ? 1 : 0,
            'ingredientes' => $ingredientes
        ];

        if ($this->modelo->crear($datos)) {
            setMensaje('success', 'Receta creada correctamente.');
        } else {
            setMensaje('danger', 'Error al crear la receta.');
        }
        redirigir('index.php?modulo=recetas&accion=index');
    }

    public function editar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=recetas&accion=index');
        }
        $receta = $this->modelo->obtenerPorId($id);
        if (!$receta) {
            setMensaje('danger', 'Receta no encontrada.');
            redirigir('index.php?modulo=recetas&accion=index');
        }
        $ingredientes_receta = $this->modelo->obtenerIngredientes($id);
        $productoModel = new Producto();
        $productos = $productoModel->obtenerTodos(true);
        $ingredienteModel = new Ingrediente();
        $ingredientes = $ingredienteModel->obtenerTodos(true);
        $unidadModel = new UnidadMedida();
        $unidades = $unidadModel->obtenerTodos(true);
        include 'includes/header.php';
        include 'views/recetas/editar.php';
        include 'includes/footer.php';
    }

    public function actualizar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=recetas&accion=index');
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=recetas&accion=index');
        }

        $nombre = sanitizar($_POST['nombre'] ?? '');
        if (empty($nombre) || empty($_POST['producto_id']) || empty($_POST['rendimiento'])) {
            setMensaje('danger', 'Nombre, producto y rendimiento son obligatorios.');
            redirigir('index.php?modulo=recetas&accion=editar&id=' . $id);
        }

        if ($this->modelo->existeNombre($nombre, $id)) {
            setMensaje('danger', 'Ya existe otra receta con ese nombre.');
            redirigir('index.php?modulo=recetas&accion=editar&id=' . $id);
        }

        // Procesar ingredientes
        $ingredientes = [];
        if (isset($_POST['ingredientes']) && is_array($_POST['ingredientes'])) {
            foreach ($_POST['ingredientes'] as $index => $ing) {
                if (!empty($ing['ingrediente_id']) && !empty($ing['cantidad']) && !empty($ing['unidad_medida_id'])) {
                    $ingredientes[] = [
                        'ingrediente_id' => intval($ing['ingrediente_id']),
                        'cantidad' => floatval($ing['cantidad']),
                        'unidad_medida_id' => intval($ing['unidad_medida_id'])
                    ];
                }
            }
        }

        if (empty($ingredientes)) {
            setMensaje('danger', 'Debes agregar al menos un ingrediente a la receta.');
            redirigir('index.php?modulo=recetas&accion=editar&id=' . $id);
        }

        $datos = [
            'producto_id' => intval($_POST['producto_id']),
            'nombre' => $nombre,
            'rendimiento' => floatval($_POST['rendimiento']),
            'unidad_rendimiento' => sanitizar($_POST['unidad_rendimiento']),
            'tiempo_preparacion' => intval($_POST['tiempo_preparacion'] ?? 0),
            'tiempo_coccion' => intval($_POST['tiempo_coccion'] ?? 0),
            'instrucciones' => sanitizar($_POST['instrucciones'] ?? ''),
            'estado' => isset($_POST['estado']) ? 1 : 0,
            'ingredientes' => $ingredientes
        ];

        if ($this->modelo->actualizar($id, $datos)) {
            setMensaje('success', 'Receta actualizada correctamente.');
        } else {
            setMensaje('danger', 'Error al actualizar la receta.');
        }
        redirigir('index.php?modulo=recetas&accion=index');
    }

    public function eliminar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id && $this->modelo->eliminar($id)) {
            setMensaje('success', 'Receta eliminada correctamente.');
        } else {
            setMensaje('danger', 'Error al eliminar la receta.');
        }
        redirigir('index.php?modulo=recetas&accion=index');
    }

    public function ver() {
        $this->verificarPermiso(false);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=recetas&accion=index');
        }
        $receta = $this->modelo->obtenerPorId($id);
        if (!$receta) {
            setMensaje('danger', 'Receta no encontrada.');
            redirigir('index.php?modulo=recetas&accion=index');
        }
        $ingredientes = $this->modelo->obtenerIngredientes($id);
        include 'includes/header.php';
        include 'views/recetas/ver.php';
        include 'includes/footer.php';
    }
}
?>