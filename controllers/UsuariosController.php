<?php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../includes/funciones.php';

class UsuariosController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Usuario();
    }

    private function verificarPermiso() {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }

        if (!esAdmin()) {
            setMensaje('danger', 'No tienes permiso para administrar usuarios.');
            redirigir('index.php');
        }
    }

    public function index() {
        $this->verificarPermiso();
        $usuarios = $this->modelo->obtenerTodos();
        include 'includes/header.php';
        include 'views/usuarios/index.php';
        include 'includes/footer.php';
    }

    public function crear() {
        $this->verificarPermiso();
        $roles = $this->modelo->obtenerRoles();
        include 'includes/header.php';
        include 'views/usuarios/crear.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=usuarios&accion=index');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $rolId = intval($_POST['rol_id'] ?? 0);
        $password = $_POST['password'] ?? '';

        if (empty($nombre) || empty($email) || empty($password) || !$rolId) {
            setMensaje('danger', 'Todos los campos obligatorios deben completarse.');
            redirigir('index.php?modulo=usuarios&accion=crear');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setMensaje('danger', 'El email ingresado no es válido.');
            redirigir('index.php?modulo=usuarios&accion=crear');
        }

        if ($this->modelo->existeEmail($email)) {
            setMensaje('danger', 'Ya existe un usuario con ese email.');
            redirigir('index.php?modulo=usuarios&accion=crear');
        }

        $datos = [
            'nombre' => sanitizar($nombre),
            'apellido' => sanitizar($_POST['apellido'] ?? ''),
            'email' => sanitizar($email),
            'telefono' => sanitizar($_POST['telefono'] ?? ''),
            'password' => $password,
            'rol_id' => $rolId,
            'activo' => isset($_POST['activo']) ? 1 : 0,
            'foto' => sanitizar($_POST['foto'] ?? '')
        ];

        if ($this->modelo->crear($datos)) {
            setMensaje('success', 'Usuario creado correctamente.');
        } else {
            setMensaje('danger', 'Error al crear el usuario.');
        }

        redirigir('index.php?modulo=usuarios&accion=index');
    }

    public function editar() {
        $this->verificarPermiso();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=usuarios&accion=index');
        }

        $usuario = $this->modelo->obtenerPorId($id);
        if (!$usuario) {
            setMensaje('danger', 'Usuario no encontrado.');
            redirigir('index.php?modulo=usuarios&accion=index');
        }

        $roles = $this->modelo->obtenerRoles();
        include 'includes/header.php';
        include 'views/usuarios/editar.php';
        include 'includes/footer.php';
    }

    public function actualizar() {
        $this->verificarPermiso();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=usuarios&accion=index');
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=usuarios&accion=index');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $rolId = intval($_POST['rol_id'] ?? 0);

        if (empty($nombre) || empty($email) || !$rolId) {
            setMensaje('danger', 'Todos los campos obligatorios deben completarse.');
            redirigir('index.php?modulo=usuarios&accion=editar&id=' . $id);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setMensaje('danger', 'El email ingresado no es válido.');
            redirigir('index.php?modulo=usuarios&accion=editar&id=' . $id);
        }

        if ($this->modelo->existeEmail($email, $id)) {
            setMensaje('danger', 'Ya existe otro usuario con ese email.');
            redirigir('index.php?modulo=usuarios&accion=editar&id=' . $id);
        }

        $datos = [
            'nombre' => sanitizar($nombre),
            'apellido' => sanitizar($_POST['apellido'] ?? ''),
            'email' => sanitizar($email),
            'telefono' => sanitizar($_POST['telefono'] ?? ''),
            'rol_id' => $rolId,
            'activo' => isset($_POST['activo']) ? 1 : 0,
            'foto' => sanitizar($_POST['foto'] ?? ''),
            'password' => $_POST['password'] ?? ''
        ];

        if ($this->modelo->actualizar($id, $datos)) {
            setMensaje('success', 'Usuario actualizado correctamente.');
        } else {
            setMensaje('danger', 'Error al actualizar el usuario.');
        }

        redirigir('index.php?modulo=usuarios&accion=index');
    }

    public function eliminar() {
        $this->verificarPermiso();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=usuarios&accion=index');
        }

        if ($this->modelo->eliminar($id)) {
            setMensaje('success', 'Usuario inhabilitado correctamente.');
        } else {
            setMensaje('danger', 'Error al inhabilitar el usuario.');
        }

        redirigir('index.php?modulo=usuarios&accion=index');
    }

    public function perfil() {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }

        $id = $_SESSION['usuario_id'];
        $usuario = $this->modelo->obtenerPorId($id);
        $roles = $this->modelo->obtenerRoles();

        include 'includes/header.php';
        include 'views/usuarios/perfil.php';
        include 'includes/footer.php';
    }
}
?>
