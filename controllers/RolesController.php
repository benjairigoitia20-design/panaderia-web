<?php
require_once __DIR__ . '/../models/Rol.php';
require_once __DIR__ . '/../includes/funciones.php';

class RolesController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Rol();
    }

    private function verificarPermiso() {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }

        if (!esAdmin()) {
            setMensaje('danger', 'No tienes permiso para administrar roles y permisos.');
            redirigir('index.php');
        }
    }

    public function index() {
        $this->verificarPermiso();
        $roles = $this->modelo->obtenerTodos();
        $permisos = $this->modelo->obtenerPermisos();
        $permisosPorRol = [];

        foreach ($roles as $rol) {
            $permisosPorRol[$rol['id']] = $this->modelo->obtenerPermisosPorRol($rol['id']);
        }

        include 'includes/header.php';
        include 'views/roles/index.php';
        include 'includes/footer.php';
    }

    public function crear() {
        $this->verificarPermiso();
        include 'includes/header.php';
        include 'views/roles/crear.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=roles&accion=index');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        if (empty($nombre)) {
            setMensaje('danger', 'El nombre del rol es obligatorio.');
            redirigir('index.php?modulo=roles&accion=crear');
        }

        if ($this->modelo->existeNombre($nombre)) {
            setMensaje('danger', 'Ya existe un rol con ese nombre.');
            redirigir('index.php?modulo=roles&accion=crear');
        }

        if ($this->modelo->crear($nombre)) {
            setMensaje('success', 'Rol creado correctamente.');
        } else {
            setMensaje('danger', 'Error al crear el rol.');
        }

        redirigir('index.php?modulo=roles&accion=index');
    }

    public function editar() {
        $this->verificarPermiso();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=roles&accion=index');
        }

        $rol = $this->modelo->obtenerPorId($id);
        if (!$rol) {
            setMensaje('danger', 'Rol no encontrado.');
            redirigir('index.php?modulo=roles&accion=index');
        }

        include 'includes/header.php';
        include 'views/roles/editar.php';
        include 'includes/footer.php';
    }

    public function actualizar() {
        $this->verificarPermiso();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=roles&accion=index');
        }

        $id = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');

        if (!$id || empty($nombre)) {
            setMensaje('danger', 'Datos inválidos para actualizar el rol.');
            redirigir('index.php?modulo=roles&accion=index');
        }

        if ($this->modelo->existeNombre($nombre, $id)) {
            setMensaje('danger', 'Ya existe otro rol con ese nombre.');
            redirigir('index.php?modulo=roles&accion=editar&id=' . $id);
        }

        if ($this->modelo->actualizar($id, $nombre)) {
            setMensaje('success', 'Rol actualizado correctamente.');
        } else {
            setMensaje('danger', 'Error al actualizar el rol.');
        }

        redirigir('index.php?modulo=roles&accion=index');
    }

    public function guardarPermisos() {
        $this->verificarPermiso();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=roles&accion=index');
        }

        $permisos = $_POST['permisos'] ?? [];

        foreach ($permisos as $rolId => $ids) {
            $rolId = intval($rolId);
            $ids = array_map('intval', $ids);
            $this->modelo->actualizarPermisos($rolId, $ids);
        }

        setMensaje('success', 'Permisos actualizados correctamente.');
        redirigir('index.php?modulo=roles&accion=index');
    }

    public function eliminar() {
        $this->verificarPermiso();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=roles&accion=index');
        }

        if ($this->modelo->eliminar($id)) {
            setMensaje('success', 'Rol eliminado correctamente.');
        } else {
            setMensaje('danger', 'No se puede eliminar el rol porque tiene usuarios asociados.');
        }

        redirigir('index.php?modulo=roles&accion=index');
    }
}
?>
