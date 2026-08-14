<?php
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../includes/funciones.php';

class CategoriasController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Categoria();
    }

    private function verificarPermiso($escritura = false) {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
        if ($escritura && !esAdmin() && !tieneRol('panadero')) {
            setMensaje('danger', 'No tienes permiso para realizar esta acción.');
            redirigir('index.php?modulo=categorias&accion=index');
        }
    }

    public function index() {
        $this->verificarPermiso(false);
        $categorias = $this->modelo->obtenerTodos(false);
        include 'includes/header.php';
        include 'views/categorias/index.php';
        include 'includes/footer.php';
    }

    public function crear() {
        $this->verificarPermiso(true);
        include 'includes/header.php';
        include 'views/categorias/crear.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=categorias&accion=crear');
        }

        $nombre = sanitizar($_POST['nombre'] ?? '');
        if (empty($nombre)) {
            setMensaje('danger', 'El nombre es obligatorio.');
            redirigir('index.php?modulo=categorias&accion=crear');
        }

        if ($this->modelo->existeNombre($nombre)) {
            setMensaje('danger', 'Ya existe una categoría con ese nombre.');
            redirigir('index.php?modulo=categorias&accion=crear');
        }

        $datos = [
            'nombre' => $nombre,
            'descripcion' => sanitizar($_POST['descripcion'] ?? ''),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        if ($this->modelo->crear($datos)) {
            setMensaje('success', 'Categoría creada correctamente.');
        } else {
            setMensaje('danger', 'Error al crear la categoría.');
        }
        redirigir('index.php?modulo=categorias&accion=index');
    }

    public function editar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=categorias&accion=index');
        }
        $categoria = $this->modelo->obtenerPorId($id);
        if (!$categoria) {
            setMensaje('danger', 'Categoría no encontrada.');
            redirigir('index.php?modulo=categorias&accion=index');
        }
        include 'includes/header.php';
        include 'views/categorias/editar.php';
        include 'includes/footer.php';
    }

    public function actualizar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=categorias&accion=index');
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=categorias&accion=index');
        }

        $nombre = sanitizar($_POST['nombre'] ?? '');
        if (empty($nombre)) {
            setMensaje('danger', 'El nombre es obligatorio.');
            redirigir('index.php?modulo=categorias&accion=editar&id=' . $id);
        }

        if ($this->modelo->existeNombre($nombre, $id)) {
            setMensaje('danger', 'Ya existe otra categoría con ese nombre.');
            redirigir('index.php?modulo=categorias&accion=editar&id=' . $id);
        }

        $datos = [
            'nombre' => $nombre,
            'descripcion' => sanitizar($_POST['descripcion'] ?? ''),
            'activo' => isset($_POST['activo']) ? 1 : 0
        ];

        if ($this->modelo->actualizar($id, $datos)) {
            setMensaje('success', 'Categoría actualizada correctamente.');
        } else {
            setMensaje('danger', 'Error al actualizar la categoría.');
        }
        redirigir('index.php?modulo=categorias&accion=index');
    }

    public function eliminar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id && $this->modelo->eliminar($id)) {
            setMensaje('success', 'Categoría eliminada correctamente.');
        } else {
            setMensaje('danger', 'No se puede eliminar: tiene productos asociados o error.');
        }
        redirigir('index.php?modulo=categorias&accion=index');
    }
}
?>