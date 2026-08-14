<?php
require_once 'includes/funciones.php';
require_once 'config/database.php';

// Verificar login
if (!estaLogueado()) {
    redirigir('views/login/login.php');
}

// Obtener módulo y acción
$modulo = isset($_GET['modulo']) ? $_GET['modulo'] : 'dashboard';
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'index';

// Cargar según módulo
if ($modulo === 'dashboard') {
    include 'includes/header.php';
    include 'views/dashboard/index.php';
    include 'includes/footer.php';
} else {
    // Cargar controlador del módulo
    $controladorArchivo = 'controllers/' . ucfirst($modulo) . 'Controller.php';
    if (file_exists($controladorArchivo)) {
        require_once $controladorArchivo;
        $nombreClase = ucfirst($modulo) . 'Controller';
        if (class_exists($nombreClase)) {
            $controller = new $nombreClase();
            if (method_exists($controller, $accion)) {
                $controller->$accion();
            } else {
                // Acción por defecto
                $controller->index();
            }
        } else {
            die("Controlador no encontrado.");
        }
    } else {
        die("Módulo no existe.");
    }
}
?>