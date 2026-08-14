<?php
session_start();

function redirigir($url) {
    header("Location: $url");
    exit;
}

function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

function tieneRol($rolNombre) {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === $rolNombre;
}

function esAdmin() {
    return tieneRol('admin');
}

function sanitizar($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Para mostrar mensajes flash
function setMensaje($tipo, $texto) {
    $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
}

function getMensaje() {
    if (isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']);
        return $mensaje;
    }
    return null;
}
?>