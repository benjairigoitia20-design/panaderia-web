<?php
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../includes/funciones.php';

class ProductosController {
    private $modelo;

    public function __construct() {
        $this->modelo = new Producto();
    }

    private function verificarPermiso($escritura = false) {
        if (!estaLogueado()) {
            redirigir('views/login/login.php');
        }
        if ($escritura && !esAdmin() && !tieneRol('panadero')) {
            setMensaje('danger', 'No tienes permiso para realizar esta acción.');
            redirigir('index.php?modulo=productos&accion=index');
        }
    }

    public function index() {
        $this->verificarPermiso(false);
        $productos = $this->modelo->obtenerTodosConCategoria(true);
        include 'includes/header.php';
        include 'views/productos/index.php';
        include 'includes/footer.php';
    }

    public function crear() {
        $this->verificarPermiso(true);
        include 'includes/header.php';
        include 'views/productos/crear.php';
        include 'includes/footer.php';
    }

    public function guardar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=productos&accion=crear');
        }

        $nombre = sanitizar($_POST['nombre'] ?? '');
        $precio = filter_var($_POST['precio'] ?? 0, FILTER_VALIDATE_FLOAT);
        if (empty($nombre) || $precio === false || $precio < 0) {
            setMensaje('danger', 'Nombre y precio válido son obligatorios.');
            redirigir('index.php?modulo=productos&accion=crear');
        }

        if ($this->modelo->existeNombre($nombre)) {
            setMensaje('danger', 'Ya existe un producto con ese nombre.');
            redirigir('index.php?modulo=productos&accion=crear');
        }

        $imagen = '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagen = $this->subirImagen($_FILES['imagen']);
        }

        $datos = [
            'nombre' => $nombre,
            'descripcion' => sanitizar($_POST['descripcion'] ?? ''),
            'precio' => $precio,
            'stock' => intval($_POST['stock'] ?? 0),
            'categoria_id' => !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null,
            'imagen' => $imagen,
            'destacado' => isset($_POST['destacado']) ? 1 : 0,
            'estado' => isset($_POST['estado']) ? 1 : 0
        ];

        if ($this->modelo->crear($datos)) {
            setMensaje('success', 'Producto creado correctamente.');
        } else {
            setMensaje('danger', 'Error al crear el producto.');
        }
        redirigir('index.php?modulo=productos&accion=index');
    }

    public function editar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            setMensaje('danger', 'ID de producto no válido.');
            redirigir('index.php?modulo=productos&accion=index');
        }
        $producto = $this->modelo->obtenerPorId($id);
        if (!$producto) {
            setMensaje('danger', 'Producto no encontrado.');
            redirigir('index.php?modulo=productos&accion=index');
        }
        include 'includes/header.php';
        include 'views/productos/editar.php';
        include 'includes/footer.php';
    }

    public function actualizar() {
        $this->verificarPermiso(true);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('index.php?modulo=productos&accion=index');
        }

        $id = intval($_POST['id'] ?? 0);
        if (!$id) {
            setMensaje('danger', 'ID no válido.');
            redirigir('index.php?modulo=productos&accion=index');
        }

        $productoExistente = $this->modelo->obtenerPorId($id);
        if (!$productoExistente) {
            setMensaje('danger', 'Producto no encontrado.');
            redirigir('index.php?modulo=productos&accion=index');
        }

        $nombre = sanitizar($_POST['nombre'] ?? '');
        $precio = filter_var($_POST['precio'] ?? 0, FILTER_VALIDATE_FLOAT);
        if (empty($nombre) || $precio === false || $precio < 0) {
            setMensaje('danger', 'Nombre y precio válido son obligatorios.');
            redirigir('index.php?modulo=productos&accion=editar&id=' . $id);
        }

        if ($this->modelo->existeNombre($nombre, $id)) {
            setMensaje('danger', 'Ya existe otro producto con ese nombre.');
            redirigir('index.php?modulo=productos&accion=editar&id=' . $id);
        }

        $imagen = $productoExistente['imagen'];
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagen = $this->subirImagen($_FILES['imagen']);
            if (!empty($productoExistente['imagen']) && file_exists(__DIR__ . '/../assets/img/' . $productoExistente['imagen'])) {
                unlink(__DIR__ . '/../assets/img/' . $productoExistente['imagen']);
            }
        }

        $datos = [
            'nombre' => $nombre,
            'descripcion' => sanitizar($_POST['descripcion'] ?? ''),
            'precio' => $precio,
            'stock' => intval($_POST['stock'] ?? 0),
            'categoria_id' => !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null,
            'imagen' => $imagen,
            'destacado' => isset($_POST['destacado']) ? 1 : 0,
            'estado' => isset($_POST['estado']) ? 1 : 0
        ];

        if ($this->modelo->actualizar($id, $datos)) {
            setMensaje('success', 'Producto actualizado correctamente.');
        } else {
            setMensaje('danger', 'Error al actualizar el producto.');
        }
        redirigir('index.php?modulo=productos&accion=index');
    }

    public function eliminar() {
        $this->verificarPermiso(true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id && $this->modelo->eliminar($id)) {
            setMensaje('success', 'Producto eliminado (desactivado).');
        } else {
            setMensaje('danger', 'Error al eliminar el producto.');
        }
        redirigir('index.php?modulo=productos&accion=index');
    }

    private function subirImagen($file) {
        $directorio = __DIR__ . '/../assets/img/';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $nombreOriginal = basename($file['name']);
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $nombreUnico = uniqid() . '.' . $extension;
        $rutaDestino = $directorio . $nombreUnico;

        $tipoPermitido = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $tipoPermitido)) {
            setMensaje('danger', 'Formato de imagen no permitido. Use JPG, PNG, GIF o WEBP.');
            redirigir('index.php?modulo=productos&accion=crear');
        }

        if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
            return $nombreUnico;
        } else {
            setMensaje('danger', 'Error al subir la imagen.');
            redirigir('index.php?modulo=productos&accion=crear');
        }
    }
}
?>