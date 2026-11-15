<?php
require_once __DIR__ . '/../models/Ingrediente.php';
require_once __DIR__ . '/../models/UnidadMedida.php';
require_once __DIR__ . '/../includes/funciones.php';

class IngredientesController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Ingrediente();
    }

    private function verificarPermiso($escritura = false) {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
        if ($escritura && !esAdmin() && !tieneRol('panadero')) {
            setMensaje('danger', 'No tienes permiso para realizar esta acción.');
            redirigir('index.php?modulo=ingredientes&accion=index');
        }
    }

    public function index() {
        $this->verificarPermiso(false);
        $ingredientes = $this->modelo->obtenerTodos(false);
        include 'includes/header.php';
        include 'views/ingredientes/index.php';
        include 'includes/footer.php';
    }

    public function crear() {
        $this->verificarPermiso(true);
        $unidadModel = new UnidadMedida();
        $unidades = $unidadModel->obtenerTodos(true);
        include 'includes/header.php';
        include 'views/ingredientes/crear.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=ingredientes&accion=crear');
        }

        $nombre = sanitizar($_POST['nombre'] ?? '');
        if (empty($nombre) || empty($_POST['unidad_medida_id'])) {
            setMensaje('danger', 'Nombre y unidad de medida son obligatorios.');
            redirigir('index.php?modulo=ingredientes&accion=crear');
        }

        if ($this->modelo->existeNombre($nombre)) {
            setMensaje('danger', 'Ya existe un ingrediente con ese nombre.');
            redirigir('index.php?modulo=ingredientes&accion=crear');
        }

        $datos = [
            'nombre' => $nombre,
            'codigo' => sanitizar($_POST['codigo'] ?? ''),
            'categoria' => sanitizar($_POST['categoria'] ?? ''),
            'unidad_medida_id' => intval($_POST['unidad_medida_id']),
            'stock_actual' => floatval($_POST['stock_actual'] ?? 0),
            'stock_minimo' => floatval($_POST['stock_minimo'] ?? 0),
            'costo_unitario' => floatval($_POST['costo_unitario'] ?? 0),
            'proveedor_principal' => sanitizar($_POST['proveedor_principal'] ?? ''),
            'fecha_vencimiento' => !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : null,
            'estado' => isset($_POST['estado']) ? 1 : 0
        ];

        if ($this->modelo->crear($datos)) {
            setMensaje('success', 'Ingrediente creado correctamente.');
        } else {
            setMensaje('danger', 'Error al crear el ingrediente.');
        }
        redirigir('index.php?modulo=ingredientes&accion=index');
    }

    public function editar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=ingredientes&accion=index');
        }
        $ingrediente = $this->modelo->obtenerPorId($id);
        if (!$ingrediente) {
            setMensaje('danger', 'Ingrediente no encontrado.');
            redirigir('index.php?modulo=ingredientes&accion=index');
        }
        $unidadModel = new UnidadMedida();
        $unidades = $unidadModel->obtenerTodos(true);
        include 'includes/header.php';
        include 'views/ingredientes/editar.php';
        include 'includes/footer.php';
    }

    public function actualizar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=ingredientes&accion=index');
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=ingredientes&accion=index');
        }

        $nombre = sanitizar($_POST['nombre'] ?? '');
        if (empty($nombre) || empty($_POST['unidad_medida_id'])) {
            setMensaje('danger', 'Nombre y unidad de medida son obligatorios.');
            redirigir('index.php?modulo=ingredientes&accion=editar&id=' . $id);
        }

        if ($this->modelo->existeNombre($nombre, $id)) {
            setMensaje('danger', 'Ya existe otro ingrediente con ese nombre.');
            redirigir('index.php?modulo=ingredientes&accion=editar&id=' . $id);
        }

        $datos = [
            'nombre' => $nombre,
            'codigo' => sanitizar($_POST['codigo'] ?? ''),
            'categoria' => sanitizar($_POST['categoria'] ?? ''),
            'unidad_medida_id' => intval($_POST['unidad_medida_id']),
            'stock_actual' => floatval($_POST['stock_actual'] ?? 0),
            'stock_minimo' => floatval($_POST['stock_minimo'] ?? 0),
            'costo_unitario' => floatval($_POST['costo_unitario'] ?? 0),
            'proveedor_principal' => sanitizar($_POST['proveedor_principal'] ?? ''),
            'fecha_vencimiento' => !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : null,
            'estado' => isset($_POST['estado']) ? 1 : 0
        ];

        if ($this->modelo->actualizar($id, $datos)) {
            setMensaje('success', 'Ingrediente actualizado correctamente.');
        } else {
            setMensaje('danger', 'Error al actualizar el ingrediente.');
        }
        redirigir('index.php?modulo=ingredientes&accion=index');
    }

    public function eliminar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id && $this->modelo->eliminar($id)) {
            setMensaje('success', 'Ingrediente eliminado correctamente.');
        } else {
            setMensaje('danger', 'Error al eliminar el ingrediente.');
        }
        redirigir('index.php?modulo=ingredientes&accion=index');
    }
}
?>