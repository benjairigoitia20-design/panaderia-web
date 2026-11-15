<?php
require_once __DIR__ . '/../models/Proveedor.php';
require_once __DIR__ . '/../includes/funciones.php';

class ProveedoresController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Proveedor();
    }

    private function verificarPermiso($escritura = false) {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
        if ($escritura && !esAdmin() && !tieneRol('encargado')) {
            setMensaje('danger', 'No tienes permiso para realizar esta acción.');
            redirigir('index.php?modulo=proveedores&accion=index');
        }
    }

    public function index() {
        $this->verificarPermiso(false);
        $proveedores = $this->modelo->obtenerTodos(false);
        include 'includes/header.php';
        include 'views/proveedores/index.php';
        include 'includes/footer.php';
    }

    public function crear() {
        $this->verificarPermiso(true);
        include 'includes/header.php';
        include 'views/proveedores/crear.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=proveedores&accion=crear');
        }

        $razon_social = sanitizar($_POST['razon_social'] ?? '');
        if (empty($razon_social)) {
            setMensaje('danger', 'La razón social es obligatoria.');
            redirigir('index.php?modulo=proveedores&accion=crear');
        }

        $cuit = sanitizar($_POST['cuit'] ?? '');
        if (!empty($cuit) && $this->modelo->existeCuit($cuit)) {
            setMensaje('danger', 'Ya existe un proveedor con ese CUIT.');
            redirigir('index.php?modulo=proveedores&accion=crear');
        }

        $datos = [
            'razon_social' => $razon_social,
            'cuit' => $cuit,
            'telefono' => sanitizar($_POST['telefono'] ?? ''),
            'email' => sanitizar($_POST['email'] ?? ''),
            'direccion' => sanitizar($_POST['direccion'] ?? ''),
            'contacto_nombre' => sanitizar($_POST['contacto_nombre'] ?? ''),
            'contacto_telefono' => sanitizar($_POST['contacto_telefono'] ?? ''),
            'observaciones' => sanitizar($_POST['observaciones'] ?? ''),
            'estado' => isset($_POST['estado']) ? 1 : 0
        ];

        if ($this->modelo->crear($datos)) {
            setMensaje('success', 'Proveedor creado correctamente.');
        } else {
            setMensaje('danger', 'Error al crear el proveedor.');
        }
        redirigir('index.php?modulo=proveedores&accion=index');
    }

    public function editar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=proveedores&accion=index');
        }
        $proveedor = $this->modelo->obtenerPorId($id);
        if (!$proveedor) {
            setMensaje('danger', 'Proveedor no encontrado.');
            redirigir('index.php?modulo=proveedores&accion=index');
        }
        include 'includes/header.php';
        include 'views/proveedores/editar.php';
        include 'includes/footer.php';
    }

    public function actualizar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=proveedores&accion=index');
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=proveedores&accion=index');
        }

        $razon_social = sanitizar($_POST['razon_social'] ?? '');
        if (empty($razon_social)) {
            setMensaje('danger', 'La razón social es obligatoria.');
            redirigir('index.php?modulo=proveedores&accion=editar&id=' . $id);
        }

        $cuit = sanitizar($_POST['cuit'] ?? '');
        if (!empty($cuit) && $this->modelo->existeCuit($cuit, $id)) {
            setMensaje('danger', 'Ya existe otro proveedor con ese CUIT.');
            redirigir('index.php?modulo=proveedores&accion=editar&id=' . $id);
        }

        $datos = [
            'razon_social' => $razon_social,
            'cuit' => $cuit,
            'telefono' => sanitizar($_POST['telefono'] ?? ''),
            'email' => sanitizar($_POST['email'] ?? ''),
            'direccion' => sanitizar($_POST['direccion'] ?? ''),
            'contacto_nombre' => sanitizar($_POST['contacto_nombre'] ?? ''),
            'contacto_telefono' => sanitizar($_POST['contacto_telefono'] ?? ''),
            'observaciones' => sanitizar($_POST['observaciones'] ?? ''),
            'estado' => isset($_POST['estado']) ? 1 : 0
        ];

        if ($this->modelo->actualizar($id, $datos)) {
            setMensaje('success', 'Proveedor actualizado correctamente.');
        } else {
            setMensaje('danger', 'Error al actualizar el proveedor.');
        }
        redirigir('index.php?modulo=proveedores&accion=index');
    }

    public function eliminar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id && $this->modelo->eliminar($id)) {
            setMensaje('success', 'Proveedor eliminado correctamente.');
        } else {
            setMensaje('danger', 'Error al eliminar el proveedor.');
        }
        redirigir('index.php?modulo=proveedores&accion=index');
    }

    public function buscar() {
        if (!estaLogueado()) {
            echo json_encode([]);
            return;
        }
        
        $termino = $_GET['termino'] ?? '';
        if (strlen($termino) < 2) {
            echo json_encode([]);
            return;
        }
        
        $proveedores = $this->modelo->buscar($termino);
        header('Content-Type: application/json');
        echo json_encode($proveedores);
    }
}
?>